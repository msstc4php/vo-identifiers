<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Mod11CheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Mod11CheckDigit::class)]
final class Mod11CheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // ISSN Manual (December 2024) 5.1.1: 0317-8471, weights 8 to 2, remainder 10 gives 1.
        yield 'ISSN manual' => ['0317847', '1'];
        // The International ISBN Agency's online users' manual, 4.4: ISBN 0-8436-1072-7, weights 10 to 2.
        yield 'ISBN-10 manual' => ['084361072', '7'];
        // "If there is no remainder, put a zero in the check digit position" (ISSN Manual).
        yield 'no remainder' => ['0000000', '0'];
        // Remainder 1 leaves a check value of 10, written X.
        yield 'ten is X' => ['0000006', 'X'];
        yield 'ten is X for nine digits' => ['000000006', 'X'];
        // Review Focus (not printed by the source): ISBN-10 0-306-40615-2 and ISSN 0378-5955.
        yield 'Review Focus ISBN-10' => ['030640615', '2'];
        yield 'Review Focus ISSN' => ['0378595', '5'];
        // A body that does not start with a zero.
        yield 'leading digit counts' => ['123456789', 'X'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckDigit(string $body, string $expected): void
    {
        self::assertSame($expected, Mod11CheckDigit::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckDigit(string $body, string $expected): void
    {
        self::assertTrue(Mod11CheckDigit::isValid($body . $expected));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(Mod11CheckDigit::isValid('03178470'));
        self::assertFalse(Mod11CheckDigit::isValid('03178472'));
        self::assertFalse(Mod11CheckDigit::isValid('0317847X'));
        self::assertFalse(Mod11CheckDigit::isValid('0000000X'));
    }

    public function testAcceptsTheShortestValue(): void
    {
        self::assertTrue(Mod11CheckDigit::isValid('00'));
        self::assertTrue(Mod11CheckDigit::isValid('0X') === false);
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(Mod11CheckDigit::isValid(''));
        self::assertFalse(Mod11CheckDigit::isValid('1'));
        self::assertFalse(Mod11CheckDigit::isValid('0317A471'));
        self::assertFalse(Mod11CheckDigit::isValid('X3178471'));
        self::assertFalse(Mod11CheckDigit::isValid('0317847x'));
    }

    public function testRejectsAnEmptyBody(): void
    {
        $this->expectException(LogicException::class);
        Mod11CheckDigit::compute('');
    }

    public function testRejectsANonDigitBody(): void
    {
        $this->expectException(LogicException::class);
        Mod11CheckDigit::compute('031784X');
    }
}
