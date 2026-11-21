<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The check digit of an air waybill number: the seven-digit serial number divided by 7, the remainder being the
 * check digit.
 *
 * @internal
 *
 * @see https://www.abf.gov.au/sdg-subsite/Pages/Check-Digit-Algorithms/Air-Waybill-Number-Validation.aspx Australian Border Force, Integrated Cargo System, "Air Waybill Number Validation" v1.1
 */
final class AirWaybillCheckDigit
{
    /**
     * @return numeric-string one digit from 0 to 6
     */
    public static function compute(string $serial): string
    {
        if (\strlen($serial) !== 7 || \strspn($serial, '0123456789') !== 7) {
            throw new LogicException('An air waybill serial number is seven ASCII digits.');
        }

        return (string) ((int) $serial % 7);
    }

    /**
     * Whether `$value` is seven serial digits followed by their check digit.
     */
    public static function isValid(string $value): bool
    {
        if (\strlen($value) !== 8 || \strspn($value, '0123456789') !== 8) {
            return false;
        }

        return self::compute(\substr($value, 0, 7)) === $value[7];
    }
}
