<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The check digit of a freight container number (owner code of three letters, equipment category letter, six digits):
 * letters take the values 10 upward leaving out the multiples of 11, the ten characters are weighted by 2 to the power
 * of their position (the first has weight 1), and the check digit is the sum modulo 11, a remainder of 10 being 0.
 *
 * @internal
 *
 * @see https://www.bic-code.org/bic-codes/ BIC, container number structure and the example BICU 123456 5 (the text of ISO 6346 is not reachable)
 */
final class Iso6346CheckDigit
{
    /** @var array<string, int> */
    private const array LETTER_VALUES = [
        'A' => 10, 'B' => 12, 'C' => 13, 'D' => 14, 'E' => 15, 'F' => 16, 'G' => 17, 'H' => 18, 'I' => 19,
        'J' => 20, 'K' => 21, 'L' => 23, 'M' => 24, 'N' => 25, 'O' => 26, 'P' => 27, 'Q' => 28, 'R' => 29,
        'S' => 30, 'T' => 31, 'U' => 32, 'V' => 34, 'W' => 35, 'X' => 36, 'Y' => 37, 'Z' => 38,
    ];

    /**
     * @return numeric-string one digit
     */
    public static function compute(string $body): string
    {
        if (\preg_match('/^[A-Z]{4}[0-9]{6}$/D', $body) !== 1) {
            throw new LogicException('A container number body is four capital letters and six digits.');
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $character = $body[$i];
            $sum += (self::LETTER_VALUES[$character] ?? (\ord($character) - 48)) << $i;
        }

        return (string) ($sum % 11 % 10);
    }

    /**
     * Whether `$value` is a four-letter code, six digits and the check digit of those ten characters.
     */
    public static function isValid(string $value): bool
    {
        if (\preg_match('/^[A-Z]{4}[0-9]{7}$/D', $value) !== 1) {
            return false;
        }

        return self::compute(\substr($value, 0, 10)) === $value[10];
    }
}
