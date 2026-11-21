<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\EcNumberCheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(EcNumberCheckDigit::class)]
final class EcNumberCheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // ECHA, "About EC and list numbers": 100-000-1 and the new-format list number A00-001-5 (A = 10).
        yield 'ECHA numeric' => ['100000', '1'];
        yield 'ECHA alphanumeric' => ['A00001', '5'];
        yield 'zero' => ['000000', '0'];
        yield 'weights' => ['123456', '3'];
        yield 'letter values' => ['ZZZZZZ', '9'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckDigit(string $body, string $expected): void
    {
        self::assertSame($expected, EcNumberCheckDigit::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckDigit(string $body, string $expected): void
    {
        self::assertTrue(EcNumberCheckDigit::isValid($body . $expected));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(EcNumberCheckDigit::isValid('1000002'));
        self::assertFalse(EcNumberCheckDigit::isValid('A000016'));
    }

    public function testNoDigitMatchesARemainderOfTen(): void
    {
        foreach (\range(0, 9) as $digit) {
            self::assertFalse(EcNumberCheckDigit::isValid('200005' . $digit));
        }
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(EcNumberCheckDigit::isValid(''));
        self::assertFalse(EcNumberCheckDigit::isValid('100000'));
        self::assertFalse(EcNumberCheckDigit::isValid('10000011'));
        self::assertFalse(EcNumberCheckDigit::isValid('100000A'));
        self::assertFalse(EcNumberCheckDigit::isValid('I000011'));
        self::assertFalse(EcNumberCheckDigit::isValid('a000011'));
        self::assertFalse(EcNumberCheckDigit::isValid("1000001\n"));
        self::assertFalse(EcNumberCheckDigit::isValid("A000015\n"));
    }

    public function testRejectsAShortBody(): void
    {
        $this->expectException(LogicException::class);
        EcNumberCheckDigit::compute('10000');
    }

    public function testRejectsALongBody(): void
    {
        $this->expectException(LogicException::class);
        EcNumberCheckDigit::compute('1000000');
    }

    public function testRejectsATrailingNewline(): void
    {
        $this->expectException(LogicException::class);
        EcNumberCheckDigit::compute("10000\n");
    }

    public function testRejectsAnExcludedLetter(): void
    {
        $this->expectException(LogicException::class);
        EcNumberCheckDigit::compute('L00001');
    }

    public function testRejectsABodyWithNoCheckDigit(): void
    {
        $this->expectException(LogicException::class);
        EcNumberCheckDigit::compute('200005');
    }
}
