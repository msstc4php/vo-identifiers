<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Ean13CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Ismn;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Ismn::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Ean13CheckDigit::class)]
final class IsmnTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'ISMN Manual example' => ['979-0-3452-4680-5', '9790345246805'];
        yield 'compact' => ['9790345246805', '9790345246805'];
        yield 'spaces' => ['979 0 3452 4680 5', '9790345246805'];
        yield 'label' => ['ISMN 979-0-3452-4680-5', '9790345246805'];
        yield 'label with colon' => ['ISMN: 979-0-3452-4680-5', '9790345246805'];
        yield 'old M form' => ['M-3452-4680-5', '9790345246805'];
        yield 'old M form with label' => ['ISMN M-3452-4680-5', '9790345246805'];
        yield 'old M compact' => ['M345246805', '9790345246805'];
        yield 'lowercase m' => ['m-3452-4680-5', '9790345246805'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheThirteenDigitForm(string $input, string $expected): void
    {
        self::assertSame($expected, new Ismn($input)->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check' => ['979-0-3452-4680-4'];
        yield 'old form wrong check' => ['M-3452-4680-4'];
        yield 'ISBN prefix' => ['978-0-306-40615-7'];
        yield 'ISBN 979-10' => ['979-10-343-0062-4'];
        yield 'twelve digits' => ['979034524680'];
        yield 'M and 13 digits' => ['M9790345246805'];
        yield 'M and eight digits' => ['M34524680'];
        yield 'trailing newline' => ["9790345246805\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Ismn($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Ismn('979-0-3452-4680-4');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISMN has a wrong check digit.', $e->getMessage());
        }

        try {
            new Ismn('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISMN has an invalid format.', $e->getMessage());
        }
    }

    public function testGtin(): void
    {
        self::assertSame('9790345246805', new Ismn('M-3452-4680-5')->toGtin()->value);
    }

    public function testOldFormEqualsNewForm(): void
    {
        self::assertTrue(new Ismn('M-3452-4680-5')->equals(new Ismn('979-0-3452-4680-5')));
    }
}
