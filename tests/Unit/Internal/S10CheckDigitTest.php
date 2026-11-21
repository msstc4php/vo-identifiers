<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\S10CheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(S10CheckDigit::class)]
final class S10CheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // UPU S10-12 5.4: serial 47312482 gives 9, "Complete self-checking number: 473124829".
        yield 'S10 example' => ['47312482', '9'];
        // "if the result is 10, use 0 as the check digit".
        yield 'ten is zero' => ['00000008', '0'];
        // "if the result is 11, use 5 as the check digit".
        yield 'eleven is five' => ['00000000', '5'];
        yield 'one' => ['00000010', '2'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckDigit(string $body, string $expected): void
    {
        self::assertSame($expected, S10CheckDigit::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheSerialWithItsCheckDigit(string $body, string $expected): void
    {
        self::assertTrue(S10CheckDigit::isValid($body . $expected));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(S10CheckDigit::isValid('473124828'));
        self::assertFalse(S10CheckDigit::isValid('000000000'));
        self::assertFalse(S10CheckDigit::isValid('000000085'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(S10CheckDigit::isValid(''));
        self::assertFalse(S10CheckDigit::isValid('47312482'));
        self::assertFalse(S10CheckDigit::isValid('4731248299'));
        self::assertFalse(S10CheckDigit::isValid('47312482X'));
        self::assertFalse(S10CheckDigit::isValid('473124829X'));
    }

    public function testRejectsAShortBody(): void
    {
        $this->expectException(LogicException::class);
        S10CheckDigit::compute('4731248');
    }

    public function testRejectsALongBody(): void
    {
        $this->expectException(LogicException::class);
        S10CheckDigit::compute('473124829');
    }

    public function testRejectsANonDigitBody(): void
    {
        $this->expectException(LogicException::class);
        S10CheckDigit::compute('4731248X');
    }
}
