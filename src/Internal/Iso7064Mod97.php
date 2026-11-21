<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The ISO/IEC 7064 MOD 97-10 check character system: letters become two digits (`A` = 10 ... `Z` = 35); a value is valid
 * when the number mod 97 is 1, and the check pair of a body is `98` minus the remainder of the body followed by `00`,
 * written with two digits. The caller arranges the characters. An EID (GSMA SGP.29) is checked as written over its 32
 * digits; a ROR identifier is checked over the decimal value of its decoded base32 characters (see `RorCheckDigits`).
 * The remainder is built digit by digit, so no big-number extension is needed.
 *
 * @internal
 *
 * @see https://www.gsma.com/solutions-and-impact/technologies/esim/wp-content/uploads/2024/03/SGP.29-v1.1.pdf GSMA SGP.29 v1.1, clause 10 (EID check digits)
 * @see https://ror.readme.io/docs/identifier ROR, "Identifier pattern" (ISO/IEC 7064:2003, two numeric check digits)
 */
final class Iso7064Mod97
{
    private const string ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @return non-empty-string two digits
     */
    public static function compute(string $body): string
    {
        if ($body === '' || \strspn($body, self::ALPHABET) !== \strlen($body)) {
            throw new LogicException('MOD 97-10 input must be digits and capital letters.');
        }

        return \sprintf('%02d', 98 - self::remainder($body . '00'));
    }

    /**
     * Whether `$value` (digits and capital letters, check characters included) leaves the remainder 1.
     */
    public static function isValid(string $value): bool
    {
        if ($value === '' || \strspn($value, self::ALPHABET) !== \strlen($value)) {
            return false;
        }

        return self::remainder($value) === 1;
    }

    /**
     * @param non-empty-string $value
     */
    private static function remainder(string $value): int
    {
        $remainder = 0;
        foreach (\str_split($value) as $character) {
            $number = \strcspn(self::ALPHABET, $character);
            $remainder = ($remainder * ($number > 9 ? 100 : 10) + $number) % 97;
        }

        return $remainder;
    }
}
