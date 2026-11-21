<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The check digit of a CAS Registry Number written without hyphens: the digits before the check digit are multiplied
 * by 1, 2, 3 ... counting from the right, and the check digit is the sum modulo 10. The body has 4 to 9 digits (a first
 * part of 2 to 7 digits and a second part of 2).
 *
 * @internal
 *
 * @see https://www.cas.org/support/documentation/chemical-substances/checkdig CAS, "Check Digit Verification of CAS Registry Numbers"
 */
final class CasCheckDigit
{
    /**
     * @return numeric-string one digit
     */
    public static function compute(string $body): string
    {
        $length = \strlen($body);
        if ($length < 4 || $length > 9 || \strspn($body, '0123456789') !== $length) {
            throw new LogicException('A CAS body is four to nine ASCII digits.');
        }

        $sum = 0;
        for ($i = 0; $i < $length; $i++) {
            $sum += (\ord($body[$length - 1 - $i]) - 48) * ($i + 1);
        }

        return (string) ($sum % 10);
    }

    /**
     * Whether `$value` is four to nine digits followed by their check digit.
     */
    public static function isValid(string $value): bool
    {
        $length = \strlen($value);
        if ($length < 5 || $length > 10 || \strspn($value, '0123456789') !== $length) {
            return false;
        }

        return self::compute(\substr($value, 0, -1)) === $value[$length - 1];
    }
}
