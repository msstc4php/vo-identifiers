<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\VinCheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(VinCheckDigit::class)]
final class VinCheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // 49 CFR 565.15(c), Table VI: sample VIN 1G4AH59H?5G118341, products add to 411, 411 mod 11 = 4.
        yield 'Table VI' => ['1G4AH59H5G118341', '4'];
        // Remainder 10 is X (Table V).
        yield 'remainder ten' => ['1M8GDM9AKP000007', 'X'];
        // Every letter value of Table III is exercised by the alphabet row.
        yield 'alphabet 1' => ['ABCDEFGHJKLMNPRS', '5'];
        yield 'alphabet 2' => ['TUVWXYZ012345678', '8'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckDigit(string $body, string $expected): void
    {
        self::assertSame($expected, VinCheckDigit::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheVinWithItsCheckDigit(string $body, string $expected): void
    {
        self::assertTrue(VinCheckDigit::isValid(\substr($body, 0, 8) . $expected . \substr($body, 8)));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(VinCheckDigit::isValid('1G4AH59H05G118341'));
        self::assertFalse(VinCheckDigit::isValid('1G4AH59H55G118341'));
        self::assertFalse(VinCheckDigit::isValid('1G4AH59HX5G118341'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(VinCheckDigit::isValid(''));
        self::assertFalse(VinCheckDigit::isValid('1G4AH59H45G11834'));
        self::assertFalse(VinCheckDigit::isValid('1G4AH59H45G1183411'));
        self::assertFalse(VinCheckDigit::isValid('1g4AH59H45G118341'));
        self::assertFalse(VinCheckDigit::isValid('1G4AH59H4IG118341'));
        self::assertFalse(VinCheckDigit::isValid('1G4AH59H45G11834O'));
        self::assertFalse(VinCheckDigit::isValid("1G4AH59H45G118341\n"));
    }

    public function testRejectsAShortBody(): void
    {
        $this->expectException(LogicException::class);
        VinCheckDigit::compute('1G4AH59H5G11834');
    }

    public function testRejectsALongBody(): void
    {
        $this->expectException(LogicException::class);
        VinCheckDigit::compute('1G4AH59H5G1183411');
    }

    public function testRejectsATrailingNewline(): void
    {
        $this->expectException(LogicException::class);
        VinCheckDigit::compute("1G4AH59H5G118341\n");
    }

    public function testRejectsAnExcludedLetter(): void
    {
        $this->expectException(LogicException::class);
        VinCheckDigit::compute('1G4AH59I5G118341');
    }
}
