<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\I18n\CountryCode;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * UN/LOCODE (UNECE Recommendation 16): an ISO 3166-1 alpha-2 country code and a three-character location code of
 * letters and the numerals 2 to 9, stored compact (`DEHAM`); a space between the parts is dropped. The code is not
 * looked up in the UN/LOCODE list, which changes twice a year.
 *
 * @see https://vocab-locode-40898c.opensource.unicc.org/recommendation16 UNECE Recommendation 16
 * @see docs/sources/IDENTIFIERS.md "UnLocode, IATA, ICAO"
 */
final class UnLocode extends AbstractStringValueObject
{
    private const string TYPE = 'UN/LOCODE';

    public function countryCode(): string
    {
        return \substr($this->value, 0, 2);
    }

    public function location(): string
    {
        return \substr($this->value, 2);
    }

    /**
     * Null when the two letters are not an ISO 3166-1 country (UN/LOCODE also uses `XZ` for installations without one).
     */
    public function country(): ?CountryCode
    {
        return CountryCode::tryFrom($this->countryCode());
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = TransportRules::compact($value);

        if (\preg_match('/^[A-Z]{2}[A-Z2-9]{3}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
