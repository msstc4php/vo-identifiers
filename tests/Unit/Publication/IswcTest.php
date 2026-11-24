<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\IswcCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Iswc;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Iswc::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(IswcCheckDigit::class)]
final class IswcTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'ISO 15707 example' => ['T-034.524.680-1', 'T0345246801'];
        yield 'compact' => ['T0345246801', 'T0345246801'];
        yield 'lowercase' => ['t0345246801', 'T0345246801'];
        yield 'label' => ['ISWC T-034.524.680-1', 'T0345246801'];
        yield 'spaces' => ['T 034 524 680 1', 'T0345246801'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheCompactForm(string $input, string $expected): void
    {
        self::assertSame($expected, (new Iswc($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'no prefix' => ['0345246801'];
        yield 'other prefix' => ['S0345246801'];
        yield 'nine digits' => ['T034524680'];
        yield 'eleven digits' => ['T03452468011'];
        yield 'letter inside' => ['T03452A6801'];
        yield 'trailing newline' => ["T0345246801\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Iswc($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Iswc('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISWC has an invalid format.', $e->getMessage());
        }
    }

    public function testCheckDigitIsNotEnforced(): void
    {
        self::assertSame('T0345246805', (new Iswc('T-034.524.680-5'))->value);
    }

    public function testReportsTheCheckDigit(): void
    {
        // ISO/TC 46/SC 9 N 268, Annex B worked example.
        self::assertTrue(new Iswc('T-034.524.680-1')->hasValidCheckDigit());
        self::assertFalse(new Iswc('T-034.524.680-5')->hasValidCheckDigit());
    }

    public function testFormatted(): void
    {
        self::assertSame('T-034.524.680-1', new Iswc('T0345246801')->formatted());
    }
}
