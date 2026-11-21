<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\AirWaybillCheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AirWaybillCheckDigit::class)]
final class AirWaybillCheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // Australian Border Force, "Air Waybill Number Validation" v1.1: serial 1234567 has remainder 5.
        yield 'ABF example' => ['1234567', '5'];
        yield 'zero' => ['0000000', '0'];
        yield 'multiple of seven' => ['0000007', '0'];
        yield 'six' => ['0000006', '6'];
        yield 'large' => ['9999999', '2'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckDigit(string $body, string $expected): void
    {
        self::assertSame($expected, AirWaybillCheckDigit::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheSerialWithItsCheckDigit(string $body, string $expected): void
    {
        self::assertTrue(AirWaybillCheckDigit::isValid($body . $expected));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(AirWaybillCheckDigit::isValid('12345674'));
        self::assertFalse(AirWaybillCheckDigit::isValid('12345677'));
        self::assertFalse(AirWaybillCheckDigit::isValid('12345679'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(AirWaybillCheckDigit::isValid(''));
        self::assertFalse(AirWaybillCheckDigit::isValid('1234567'));
        self::assertFalse(AirWaybillCheckDigit::isValid('123456755'));
        self::assertFalse(AirWaybillCheckDigit::isValid('1234567X'));
        self::assertFalse(AirWaybillCheckDigit::isValid('12345675X'));
    }

    public function testRejectsAShortBody(): void
    {
        $this->expectException(LogicException::class);
        AirWaybillCheckDigit::compute('123456');
    }

    public function testRejectsALongBody(): void
    {
        $this->expectException(LogicException::class);
        AirWaybillCheckDigit::compute('12345678');
    }

    public function testRejectsANonDigitBody(): void
    {
        $this->expectException(LogicException::class);
        AirWaybillCheckDigit::compute('123456X');
    }
}
