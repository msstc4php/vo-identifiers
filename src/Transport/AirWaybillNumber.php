<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\AirWaybillCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Air waybill number: the carrier's three-digit IATA prefix, a seven-digit serial number and a check digit, the serial
 * number modulo 7 (IATA Resolution 600a). The hyphen after the prefix and a space between the fourth and fifth serial
 * digits, as IATA prints them, are optional and dropped; the stored form is the eleven digits.
 *
 * @see https://www.iata.org/contentassets/e9cb5a72b88f4f68a5cfc572a50b60c9/cah807-en-final.pdf IATA Cargo Agent's Handbook, Resolution 807, Edition 48 (Resolution 600a)
 * @see https://www.abf.gov.au/sdg-subsite/Pages/Check-Digit-Algorithms/Air-Waybill-Number-Validation.aspx Australian Border Force, Air Waybill Number Validation
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "AirWaybillNumber"
 */
final class AirWaybillNumber extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 11;

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
        if (\preg_match('/^([0-9]{3})-?([0-9]{4}) ?([0-9]{4})$/D', $value, $match) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        $value = $match[1] . $match[2] . $match[3];

        if (!AirWaybillCheckDigit::isValid(\substr($value, 3))) {
            throw TransportRules::fail(self::TYPE, Reason::WRONG_CHECK_DIGIT);
        }

        return $value;
    }
}
