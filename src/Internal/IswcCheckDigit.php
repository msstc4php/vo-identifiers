<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The check digit of an ISWC with the prefix `T`: `sumprod = 1 + sum(i * d_i)` over the nine digits of the work
 * identifier (the `T` counts 1 with weight 1), the check digit is `10 - sumprod mod 10`, or 0 when the remainder is 0.
 * The source is the project leader's Annex B text in an ISO/TC 46/SC 9 committee document, not the published standard.
 * N 268 does not define the weight `w_i` in words; `w_i = i` is read from the weight row of its worked example.
 *
 * @internal
 *
 * @see https://groups.niso.org/higherlogic/ws/public/download/2544/sc9n268.pdf ISO/TC 46/SC 9 N 268, response to comment 32 (Annex B)
 */
final class IswcCheckDigit
{
    /**
     * @return numeric-string one digit
     */
    public static function compute(string $workIdentifier): string
    {
        if (\strlen($workIdentifier) !== 9 || \strspn($workIdentifier, '0123456789') !== 9) {
            throw new LogicException('An ISWC work identifier is nine ASCII digits.');
        }

        $sum = 1;
        for ($i = 0; $i < 9; $i++) {
            $sum += (\ord($workIdentifier[$i]) - 48) * ($i + 1);
        }

        $remainder = $sum % 10;

        return $remainder === 0 ? '0' : (string) (10 - $remainder);
    }

    /**
     * Whether `$value` is the nine digits of the work identifier followed by their check digit.
     */
    public static function isValid(string $value): bool
    {
        if (\strlen($value) !== 10 || \strspn($value, '0123456789') !== 10) {
            return false;
        }

        return self::compute(\substr($value, 0, 9)) === $value[9];
    }
}
