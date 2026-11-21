<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The ISO/IEC 7064 MOD 97-10 check character system as ISO 17442, ISO 11649 and GSMA SGP.29 apply it: letters become two digits
 * (`A` = 10 ... `Z` = 35); a value is valid when the number mod 97 is 1, and the check pair of a body is `98` minus
 * the remainder of the body followed by `00`, written with two digits. The caller arranges the characters (an LEI is
 * checked as written; an RF creditor reference has its first four characters moved to the end first). The remainder is
 * built digit by digit, so no big-number extension is needed.
 *
 * @internal
 *
 * @see https://www.iso.org/standard/59771.html ISO 17442:2012, clause 5 and Annex A
 * @see https://www.iso.org/standard/50649.html ISO 11649:2009, clause 6 and Annex B
 * @see https://www.gsma.com/solutions-and-impact/technologies/esim/wp-content/uploads/2024/03/SGP.29-v1.1.pdf GSMA SGP.29 v1.1, clause 10 (EID check digits)
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
