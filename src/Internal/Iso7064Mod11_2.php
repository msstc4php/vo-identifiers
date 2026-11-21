<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The ISO/IEC 7064 MOD 11-2 check character of an ISNI and of an ORCID iD: for each digit the running total plus the
 * digit is doubled, the check value is `(12 - total mod 11) mod 11`, and 10 is written `X`. This is the weighted sum of
 * ISO 27729 Annex A (weights 2 to the power of the position from the right, mod 11) in Horner form.
 *
 * @internal
 *
 * @see https://standards.iteh.ai/catalog/standards/iso/6ba7c022-bf45-4860-a066-ff57650ab09e/iso-27729-2012 ISO 27729:2012, clause 4.4 and annex A
 * @see https://support.orcid.org/hc/en-us/articles/360006897674 ORCID, "Structure of the ORCID Identifier" (generateCheckDigit)
 */
final class Iso7064Mod11_2
{
    /**
     * @return non-empty-string one digit or `X`
     */
    public static function compute(string $body): string
    {
        $length = \strlen($body);
        if ($length === 0 || \strspn($body, '0123456789') !== $length) {
            throw new LogicException('A MOD 11-2 body is ASCII digits.');
        }

        $total = 0;
        for ($i = 0; $i < $length; $i++) {
            $total = ($total + \ord($body[$i]) - 48) * 2;
        }

        $check = (12 - $total % 11) % 11;

        return $check === 10 ? 'X' : (string) $check;
    }

    /**
     * Whether the last character of `$value` is the check character of the digits before it.
     */
    public static function isValid(string $value): bool
    {
        $length = \strlen($value);
        if ($length < 2 || \strspn($value, '0123456789', 0, $length - 1) !== $length - 1) {
            return false;
        }

        return self::compute(\substr($value, 0, -1)) === $value[$length - 1];
    }
}
