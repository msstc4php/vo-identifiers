<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The two check digits of a ROR identifier. The six Crockford base32 characters after the leading `0` are decoded to
 * the integer `n`; the check pair is ISO/IEC 7064 MOD 97-10 over the decimal digits of `n` (`98 - (n * 100 mod 97)`).
 * The characters are decoded, not converted letter by letter, so `Iso7064Mod97` alone cannot check a ROR.
 *
 * @internal
 *
 * @see https://ror.readme.io/docs/identifier ROR, "Identifier pattern" (Crockford base32, checksum following ISO/IEC 7064:2003)
 */
final class RorCheckDigits
{
    private const string ALPHABET = '0123456789abcdefghjkmnpqrstvwxyz';

    /**
     * @return non-empty-string two digits
     */
    public static function compute(string $body): string
    {
        if (\preg_match('/^[0-9a-hjkmnp-tv-zA-HJKMNP-TV-Z]{6}$/D', $body) !== 1) {
            throw new LogicException('A ROR body is six Crockford base32 characters.');
        }

        $number = 0;
        foreach (\str_split(\strtolower($body)) as $character) {
            $number = $number * 32 + \strcspn(self::ALPHABET, $character);
        }

        return Iso7064Mod97::compute((string) $number);
    }

    /**
     * Whether `$value` is six Crockford base32 characters followed by their two check digits.
     */
    public static function isValid(string $value): bool
    {
        if (\preg_match('/^([0-9a-hjkmnp-tv-zA-HJKMNP-TV-Z]{6})([0-9]{2})$/D', $value, $parts) !== 1) {
            return false;
        }

        return self::compute($parts[1]) === $parts[2];
    }
}
