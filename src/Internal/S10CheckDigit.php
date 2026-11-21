<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The check digit of a UPU S10 item identifier: the eight serial digits are multiplied by 8 6 4 2 3 5 9 7, and the
 * check digit is 11 minus the remainder of the sum divided by 11, a result of 10 being 0 and a result of 11 being 5.
 *
 * @internal
 *
 * @see https://upu.int/UPU/media/upu/files/postalSolutions/programmesAndServices/standards/S10-12.pdf UPU S10-12, 5.4
 */
final class S10CheckDigit
{
    /** @var list<int> */
    private const array WEIGHTS = [8, 6, 4, 2, 3, 5, 9, 7];

    /**
     * @return numeric-string one digit
     */
    public static function compute(string $serial): string
    {
        if (\strlen($serial) !== 8 || \strspn($serial, '0123456789') !== 8) {
            throw new LogicException('An S10 serial number is eight ASCII digits.');
        }

        $sum = 0;
        foreach (self::WEIGHTS as $i => $weight) {
            $sum += (\ord($serial[$i]) - 48) * $weight;
        }

        return (string) match ($check = 11 - $sum % 11) {
            10 => 0,
            11 => 5,
            default => $check,
        };
    }

    /**
     * Whether `$value` is eight serial digits followed by their check digit.
     */
    public static function isValid(string $value): bool
    {
        if (\strlen($value) !== 9 || \strspn($value, '0123456789') !== 9) {
            return false;
        }

        return self::compute(\substr($value, 0, 8)) === $value[8];
    }
}
