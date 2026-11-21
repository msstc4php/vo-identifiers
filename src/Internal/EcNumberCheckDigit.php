<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The check digit of an EC or list number: `(N1 + 2 N2 + 3 N3 + 4 N4 + 5 N5 + 6 N6) mod 11` over the six characters
 * before it, a letter taking the value 10 for A to 35 for Z (I, L and O are not used). A remainder of 10 has no digit,
 * so no number carries it.
 *
 * @internal
 *
 * @see https://echa.europa.eu/about-ec-and-list-numbers ECHA, "About EC and list numbers", "How to calculate the check digit"
 */
final class EcNumberCheckDigit
{
    private const string VALUES = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private const string BODY_PATTERN = '/^[0-9A-HJKMNP-Z]{6}$/D';

    /**
     * @return numeric-string one digit
     */
    public static function compute(string $body): string
    {
        if (\preg_match(self::BODY_PATTERN, $body) !== 1) {
            throw new LogicException('An EC number body is six digits or capital letters other than I, L and O.');
        }

        $remainder = self::remainder($body);
        if ($remainder === 10) {
            throw new LogicException('No EC number carries a remainder of ten.');
        }

        return (string) $remainder;
    }

    /**
     * Whether `$value` is six characters followed by their check digit.
     */
    public static function isValid(string $value): bool
    {
        if (\preg_match('/^[0-9A-HJKMNP-Z]{6}[0-9]$/D', $value) !== 1) {
            return false;
        }

        return (string) self::remainder(\substr($value, 0, 6)) === $value[6];
    }

    private static function remainder(string $body): int
    {
        $sum = 0;
        foreach (\str_split($body) as $i => $character) {
            $sum += \strcspn(self::VALUES, $character) * ($i + 1);
        }

        return $sum % 11;
    }
}
