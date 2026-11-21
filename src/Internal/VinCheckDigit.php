<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The position-nine check digit of a VIN under 49 CFR 565.15(c): digits keep their value, letters take the values of
 * Table III (I, O and Q have none), the sixteen other characters are multiplied by the weights of Table IV, and the
 * sum modulo 11 is the check digit, a remainder of 10 being `X`. The body is positions 1 to 8 followed by positions
 * 10 to 17.
 *
 * @internal
 *
 * @see https://www.law.cornell.edu/cfr/text/49/565.15 49 CFR 565.15(c), Tables III to VI
 */
final class VinCheckDigit
{
    /** @var array<string, int> */
    private const array LETTER_VALUES = [
        'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8,
        'J' => 1, 'K' => 2, 'L' => 3, 'M' => 4, 'N' => 5, 'P' => 7, 'R' => 9,
        'S' => 2, 'T' => 3, 'U' => 4, 'V' => 5, 'W' => 6, 'X' => 7, 'Y' => 8, 'Z' => 9,
    ];

    /** @var list<int> positions 1-8 and 10-17 */
    private const array WEIGHTS = [8, 7, 6, 5, 4, 3, 2, 10, 9, 8, 7, 6, 5, 4, 3, 2];

    /**
     * @return non-empty-string one digit or `X`
     */
    public static function compute(string $body): string
    {
        if (\preg_match('/^[0-9A-HJ-NPR-Z]{16}$/D', $body) !== 1) {
            throw new LogicException('A VIN body is sixteen digits and capital letters without I, O and Q.');
        }

        $sum = 0;
        foreach (self::WEIGHTS as $i => $weight) {
            $character = $body[$i];
            $sum += (self::LETTER_VALUES[$character] ?? (\ord($character) - 48)) * $weight;
        }

        $check = $sum % 11;

        return $check === 10 ? 'X' : (string) $check;
    }

    /**
     * Whether `$value` is a 17-character VIN whose ninth character is the check digit of the other sixteen.
     */
    public static function isValid(string $value): bool
    {
        if (\preg_match('/^[0-9A-HJ-NPR-Z]{8}[0-9X][0-9A-HJ-NPR-Z]{8}$/D', $value) !== 1) {
            return false;
        }

        return self::compute(\substr($value, 0, 8) . \substr($value, 9)) === $value[8];
    }
}
