<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Luhn::class)]
final class LuhnTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // 3GPP TS 23.003 V17.7.0 annex B.3: the IMEI 26053179311383 has check digit 7.
        yield '3GPP B.3 IMEI' => ['26053179311383', '7'];
        // Commission Decision 2007/756/EC, Appendix 6 part 3: 33 84 4796 100-8 and 31 51 3320 198-0.
        yield 'EU wagon 1' => ['33844796100', '8'];
        yield 'EU wagon 2' => ['31513320198', '0'];
        yield 'single digit' => ['0', '0'];
        yield 'doubling carries' => ['5', '9'];
        yield 'doubled nine' => ['9', '1'];
        yield 'two digits' => ['18', '2'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckDigit(string $body, string $expected): void
    {
        self::assertSame($expected, Luhn::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckDigit(string $body, string $expected): void
    {
        self::assertTrue(Luhn::isValid($body . $expected));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(Luhn::isValid('260531793113838'));
        self::assertFalse(Luhn::isValid('260531793113830'));
    }

    public function testRejectsTooShortAndNonDigitValues(): void
    {
        self::assertFalse(Luhn::isValid(''));
        self::assertFalse(Luhn::isValid('5'));
        self::assertFalse(Luhn::isValid('26053179311383A'));
    }

    public function testRejectsNonDigitsInTheBody(): void
    {
        $this->expectException(LogicException::class);
        Luhn::compute('12A4');
    }

    public function testRejectsAnEmptyBody(): void
    {
        $this->expectException(LogicException::class);
        Luhn::compute('');
    }
}
