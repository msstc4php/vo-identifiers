<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Ean13CheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Ean13CheckDigit::class)]
final class Ean13CheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // ISBN Users' Manual 7th edition, A1.1: 978-92-95055-12-4.
        yield 'ISBN manual 7th' => ['978929505512', '4'];
        // ISBN Users' Manual 5th and 6th editions, 4.5: 978-0-11-000222-4.
        yield 'ISBN manual 5th' => ['978011000222', '4'];
        // ISMN Users' Manual 2016, 2.1.4: 979-0-3452-4680-5.
        yield 'ISMN manual' => ['979034524680', '5'];
        // An apparent check digit of 10 is 0 (ISBN Users' Manual, A1.1).
        yield 'apparent ten' => ['000000000000', '0'];
        yield 'one weighted' => ['000000000010', '9'];
        yield 'three weighted' => ['000000000001', '7'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckDigit(string $body, string $expected): void
    {
        self::assertSame($expected, Ean13CheckDigit::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckDigit(string $body, string $expected): void
    {
        self::assertTrue(Ean13CheckDigit::isValid($body . $expected));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(Ean13CheckDigit::isValid('9789295055125'));
        self::assertFalse(Ean13CheckDigit::isValid('9790345246800'));
    }

    public function testRejectsAValueThatIsNotThirteenDigits(): void
    {
        self::assertFalse(Ean13CheckDigit::isValid('978929505512'));
        self::assertFalse(Ean13CheckDigit::isValid('97892950551244'));
        self::assertFalse(Ean13CheckDigit::isValid('978929505512X'));
        self::assertFalse(Ean13CheckDigit::isValid('9789295055124X'));
        self::assertFalse(Ean13CheckDigit::isValid(''));
    }

    public function testRejectsABodyOfTheWrongLength(): void
    {
        $this->expectException(LogicException::class);
        Ean13CheckDigit::compute('97892950551');
    }

    public function testRejectsANonDigitBody(): void
    {
        $this->expectException(LogicException::class);
        Ean13CheckDigit::compute('97892950551X');
    }

    public function testRejectsAnOverlongBody(): void
    {
        $this->expectException(LogicException::class);
        Ean13CheckDigit::compute('9789295055124');
    }
}
