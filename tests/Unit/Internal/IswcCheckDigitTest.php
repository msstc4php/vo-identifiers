<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\IswcCheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IswcCheckDigit::class)]
final class IswcCheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function workIdentifiers(): iterable
    {
        // ISO/TC 46/SC 9 N 268, Annex B worked example: sumprod = 179, rem = 9, dc = 1, "T-034.524.680-1 is a valid ISWC".
        yield 'SC 9 N 268 example' => ['034524680', '1'];
        // Synthetic: sumprod = 1 + 9 = 10, remainder 0 gives 0.
        yield 'remainder zero' => ['000000001', '0'];
        // Synthetic: sumprod = 1, remainder 1 gives 9.
        yield 'all zeros' => ['000000000', '9'];
        // Synthetic: weight 9 on the last digit, sumprod = 1 + 81 = 82.
        yield 'last weight' => ['000000009', '8'];
        // Synthetic: weight 1 on the first digit, sumprod = 1 + 9 = 10.
        yield 'first weight' => ['900000000', '0'];
    }

    #[DataProvider('workIdentifiers')]
    public function testComputesTheCheckDigit(string $workIdentifier, string $expected): void
    {
        self::assertSame($expected, IswcCheckDigit::compute($workIdentifier));
    }

    #[DataProvider('workIdentifiers')]
    public function testAcceptsTheWorkIdentifierWithItsCheckDigit(string $workIdentifier, string $expected): void
    {
        self::assertTrue(IswcCheckDigit::isValid($workIdentifier . $expected));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(IswcCheckDigit::isValid('0345246805'));
        self::assertFalse(IswcCheckDigit::isValid('0345246800'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(IswcCheckDigit::isValid(''));
        self::assertFalse(IswcCheckDigit::isValid('034524681'));
        self::assertFalse(IswcCheckDigit::isValid('03452468011'));
        self::assertFalse(IswcCheckDigit::isValid('T034524680'));
        self::assertFalse(IswcCheckDigit::isValid('03452468A1'));
    }

    public function testRejectsAShortWorkIdentifier(): void
    {
        $this->expectException(LogicException::class);
        IswcCheckDigit::compute('03452468');
    }

    public function testRejectsALongWorkIdentifier(): void
    {
        $this->expectException(LogicException::class);
        IswcCheckDigit::compute('0345246801');
    }

    public function testRejectsANonDigitWorkIdentifier(): void
    {
        $this->expectException(LogicException::class);
        IswcCheckDigit::compute('03452468X');
    }
}
