<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The Luhn modulus 10 check digit: from the right of the body, every second digit, starting with the rightmost, is
 * doubled and a two-digit product is cross-added; the check digit is the tens complement of the sum. A body that is not
 * ASCII digits throws \LogicException.
 *
 * The length of the body is the caller's to fix.
 *
 * @internal
 *
 * @see https://www.etsi.org/deliver/etsi_ts/123000_123099/123003/17.07.00_60/ts_123003v170700p.pdf 3GPP TS 23.003 V17.7.0, annex B (IMEI check digit)
 * @see https://legislation.gov.uk/eudn/2007/756/annex/appendix/6/part/3/2020-12-31 Commission Decision 2007/756/EC, Appendix 6 part 3 (wagon number check digit)
 */
final class Luhn
{
    /**
     * @return numeric-string one digit
     */
    public static function compute(string $digits): string
    {
        if ($digits === '' || \strspn($digits, '0123456789') !== \strlen($digits)) {
            throw new LogicException('Luhn input must be ASCII digits.');
        }

        return (string) ((10 - self::sum($digits, true) % 10) % 10);
    }

    /**
     * Whether the last character of `$value` is the check digit of the digits before it; false for fewer than two
     * characters or any non-digit.
     */
    public static function isValid(string $value): bool
    {
        if (\strlen($value) < 2 || \strspn($value, '0123456789') !== \strlen($value)) {
            return false;
        }

        return self::sum($value, false) % 10 === 0;
    }

    /**
     * @param non-empty-string $digits
     */
    private static function sum(string $digits, bool $withoutCheckDigit): int
    {
        $sum = 0;
        $double = $withoutCheckDigit;
        for ($i = \strlen($digits) - 1; $i >= 0; $i--) {
            $digit = \ord($digits[$i]) - 48;
            if ($double) {
                $digit *= 2;
                $digit = \intdiv($digit, 10) + $digit % 10;
            }
            $sum += $digit;
            $double = !$double;
        }

        return $sum;
    }
}
