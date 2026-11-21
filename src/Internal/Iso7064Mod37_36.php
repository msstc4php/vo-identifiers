<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The ISO 7064 MOD 37,36 hybrid check character of an ISAN (digits and capital letters, values 0 to 35). The running
 * product starts at 36; each character is added, 36 is subtracted when the sum reaches 36 and a sum of 0 becomes 36,
 * the sum is doubled and 37 is subtracted when the product reaches 37. The check value is 0 when the final product is
 * 1, otherwise 37 minus the product.
 *
 * The length of the body is the caller's to fix.
 *
 * @internal
 *
 * @see https://isan.org/docs/isan_check_digit_calculation_v2.0.pdf ISAN International Agency, "ISAN Check Characters Calculation" v2.0, sections 2 and 3 and annex 1
 */
final class Iso7064Mod37_36
{
    private const string ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @return non-empty-string one digit or capital letter
     */
    public static function compute(string $body): string
    {
        $length = \strlen($body);
        if ($length === 0 || \strspn($body, self::ALPHABET) !== $length) {
            throw new LogicException('A MOD 37,36 body is digits and capital letters.');
        }

        $product = 36;
        for ($i = 0; $i < $length; $i++) {
            $sum = ($product + \strcspn(self::ALPHABET, $body[$i])) % 36;
            if ($sum === 0) {
                $sum = 36;
            }
            $product = $sum * 2 % 37;
        }

        $check = $product === 1 ? 0 : 37 - $product;

        return $check < 10 ? (string) $check : \chr(55 + $check);
    }

    /**
     * Whether the last character of `$value` is the check character of the characters before it.
     */
    public static function isValid(string $value): bool
    {
        $length = \strlen($value);
        if ($length < 2 || \strspn($value, self::ALPHABET) !== $length) {
            return false;
        }

        return self::compute(\substr($value, 0, -1)) === $value[$length - 1];
    }
}
