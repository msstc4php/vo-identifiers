<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The check character of an Energy Identification Code, used only to validate an allocated code (ENTSO-E allows no
 * other use of the algorithm): the fifteen characters take the values 0 to 9, A = 10 to Z = 35 and `-` = 36, are weighted
 * 16 down to 2, and the check value is `36 - ((sum - 1) mod 37)`. A value of 36 would be `-`, which no allocated code
 * carries.
 *
 * @internal
 *
 * @see https://eepublicdownloads.entsoe.eu/clean-documents/EDI/Library/cim_based/EIC_Data_Exchange_IG_v1.2.pdf ENTSO-E, EIC Data Exchange Implementation Guide v1.2, "The ENTSO-E check character algorithm"
 */
final class EicCheckDigit
{
    private const string VALUES = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-';

    /**
     * @return non-empty-string one digit or capital letter
     */
    public static function compute(string $body): string
    {
        if (\preg_match('/^[0-9A-Z-]{15}$/D', $body) !== 1) {
            throw new LogicException('An EIC body is fifteen digits, capital letters or hyphens.');
        }

        $check = self::checkValue($body);
        if ($check === 36) {
            throw new LogicException('No allocated EIC has a hyphen as its check character.');
        }

        return self::character($check);
    }

    /**
     * Whether `$value` is fifteen characters followed by their check character.
     */
    public static function isValid(string $value): bool
    {
        if (\preg_match('/^[0-9A-Z-]{15}[0-9A-Z]$/D', $value) !== 1) {
            return false;
        }

        return self::character(self::checkValue(\substr($value, 0, 15))) === $value[15];
    }

    /**
     * @return non-empty-string
     */
    private static function character(int $value): string
    {
        return $value < 10 ? (string) $value : \chr(55 + $value);
    }

    private static function checkValue(string $body): int
    {
        $sum = 0;
        foreach (\str_split($body) as $i => $character) {
            $sum += \strcspn(self::VALUES, $character) * (16 - $i);
        }

        return 36 - ($sum - 1 + 37) % 37;
    }
}
