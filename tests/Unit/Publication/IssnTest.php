<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Ean13CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Mod11CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Issn;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Issn::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Ean13CheckDigit::class)]
#[UsesClass(Mod11CheckDigit::class)]
final class IssnTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'ISSN Manual example' => ['0317-8471', '0317-8471'];
        yield 'review focus' => ['0378-5955', '0378-5955'];
        yield 'X' => ['2434-561X', '2434-561X'];
        yield 'lowercase x' => ['2434-561x', '2434-561X'];
        yield 'no hyphen' => ['03178471', '0317-8471'];
        yield 'space' => ['0317 8471', '0317-8471'];
        yield 'label' => ['ISSN 0317-8471', '0317-8471'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheHyphenatedForm(string $input, string $expected): void
    {
        self::assertSame($expected, (new Issn($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check' => ['0317-8472'];
        yield 'wrong check X' => ['2434-5610'];
        yield 'X inside' => ['03X7-8471'];
        yield 'nine digits' => ['0317-84711'];
        yield 'six digits' => ['0317-84'];
        yield 'seven digits without the check' => ['0317847'];
        yield 'seven digits hyphenated' => ['0317-847'];
        yield 'letters' => ['ABCD-EFGH'];
        yield 'trailing newline' => ["0317-8471\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Issn($value);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function sevenDigits(): iterable
    {
        yield 'ISSN Manual example' => ['0317847', '0317-8471'];
        yield 'check character X' => ['2434561', '2434-561X'];
        yield 'remainder zero' => ['0000000', '0000-0000'];
    }

    #[DataProvider('sevenDigits')]
    public function testFromPartsComputesTheCheckCharacter(string $sevenDigits, string $expected): void
    {
        self::assertSame($expected, Issn::fromParts($sevenDigits)->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function notSevenDigits(): iterable
    {
        yield 'six digits' => ['031784'];
        yield 'eight digits' => ['03178471'];
        yield 'hyphenated' => ['0317-847'];
        yield 'X' => ['031784X'];
        yield 'label' => ['ISSN 0317847'];
        yield 'trailing newline' => ["0317847\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('notSevenDigits')]
    public function testFromPartsRefusesAnythingButSevenDigits(string $value): void
    {
        try {
            Issn::fromParts($value);
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISSN has an invalid format.', $e->getMessage());
        }
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Issn('0317-8472');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISSN has a wrong check digit.', $e->getMessage());
        }

        try {
            new Issn('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISSN has an invalid format.', $e->getMessage());
        }
    }

    public function testGtin(): void
    {
        $issn = new Issn('0317-8471');

        self::assertSame('9770317847001', $issn->toGtin()->value);
        self::assertSame('9770317847018', $issn->toGtin('01')->value);
        self::assertSame('9772434561006', new Issn('2434-561X')->toGtin()->value);
    }

    #[DataProvider('badVariants')]
    public function testGtinRefusesABadVariant(string $variant): void
    {
        try {
            new Issn('0317-8471')->toGtin($variant);
            self::fail('Expected an exception.');
        } catch (InvalidArgumentException $e) {
            self::assertNotInstanceOf(InvalidPublicationIdentifierException::class, $e);
            self::assertSame('ISSN GTIN variant must be two digits.', $e->getMessage());
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function badVariants(): iterable
    {
        yield 'one digit' => ['0'];
        yield 'three digits' => ['001'];
        yield 'letters' => ['AB'];
        yield 'trailing newline' => ["0\n"];
        yield 'empty' => [''];
    }
}
