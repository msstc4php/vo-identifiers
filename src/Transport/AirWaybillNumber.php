<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\AirWaybillCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Air waybill number: the carrier's three-digit IATA prefix, a seven-digit serial number and a check digit, the serial
 * number modulo 7. The hyphen after the prefix, as the sources print it, is optional and dropped; the stored form is the eleven digits. The rule rests on the
 * Australian Border Force's published algorithm; IATA Resolution 600a was not reachable.
 *
 * @see https://www.abf.gov.au/sdg-subsite/Pages/Check-Digit-Algorithms/Air-Waybill-Number-Validation.aspx Australian Border Force, Air Waybill Number Validation
 * @see docs/sources/IDENTIFIERS.md "AirWaybillNumber"
 */
final class AirWaybillNumber extends AbstractStringValueObject
{
    private const string TYPE = 'Air waybill number';

    public function airlinePrefix(): string
    {
        return \substr($this->value, 0, 3);
    }

    public function serialNumber(): string
    {
        return \substr($this->value, 3, 7);
    }

    public function checkDigit(): string
    {
        return $this->value[10];
    }

    /**
     * @return non-empty-string `020-12345675`
     */
    public function formatted(): string
    {
        return $this->airlinePrefix() . '-' . \substr($this->value, 3);
    }

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('/^([0-9]{3})-?([0-9]{8})$/D', $value, $match) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        $value = $match[1] . $match[2];

        if (!AirWaybillCheckDigit::isValid(\substr($value, 3))) {
            throw TransportRules::fail(self::TYPE, 'has a wrong check digit');
        }

        return $value;
    }
}
