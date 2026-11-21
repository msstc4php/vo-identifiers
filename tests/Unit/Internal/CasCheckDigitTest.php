<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CasCheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(CasCheckDigit::class)]
final class CasCheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // CAS, "Check Digit Verification of CAS Registry Numbers": RN 107-07-3, (5x1)+(4x0)+(3x7)+(2x0)+(1x7) = 33.
        yield 'CAS example' => ['10707', '3'];
        // Review Focus (not printed by the source): water, 7732-18-5.
        yield 'Review Focus water' => ['773218', '5'];
        yield 'shortest body' => ['0000', '0'];
        yield 'longest body' => ['000000001', '1'];
        yield 'weight two' => ['000010', '2'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckDigit(string $body, string $expected): void
    {
        self::assertSame($expected, CasCheckDigit::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckDigit(string $body, string $expected): void
    {
        self::assertTrue(CasCheckDigit::isValid($body . $expected));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(CasCheckDigit::isValid('107074'));
        self::assertFalse(CasCheckDigit::isValid('107070'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(CasCheckDigit::isValid(''));
        self::assertFalse(CasCheckDigit::isValid('1073'));
        self::assertFalse(CasCheckDigit::isValid('00000000003'));
        self::assertFalse(CasCheckDigit::isValid('10707X'));
        self::assertFalse(CasCheckDigit::isValid('107-07-3'));
    }

    public function testRejectsAShortBody(): void
    {
        $this->expectException(LogicException::class);
        CasCheckDigit::compute('107');
    }

    public function testRejectsALongBody(): void
    {
        $this->expectException(LogicException::class);
        CasCheckDigit::compute('0000000001');
    }

    public function testRejectsANonDigitBody(): void
    {
        $this->expectException(LogicException::class);
        CasCheckDigit::compute('1070X');
    }
}
