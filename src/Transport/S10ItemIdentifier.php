<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\I18n\CountryCode;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Identifiers\Internal\S10CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * UPU S10 postal item identifier: a two-letter service indicator, eight serial digits, a check digit and the two-letter
 * UPU country code of the origin, for example `RA473124829CH`. Spaces are dropped and the stored form is compact. The
 * service indicators JA-JZ, KA-KZ, SA-SZ, TA-TZ and WA-WZ are reserved and cannot be assigned, so they are refused.
 *
 * @see https://upu.int/UPU/media/upu/files/postalSolutions/programmesAndServices/standards/S10-12.pdf UPU S10-12, 5.4
 * @see docs/sources/IDENTIFIERS.md "S10ItemIdentifier"
 */
final class S10ItemIdentifier extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 13;

    private const string TYPE = 'S10 item identifier';

    public function serviceIndicator(): string
    {
        return \substr($this->value, 0, 2);
    }

    public function serialNumber(): string
    {
        return \substr($this->value, 2, 8);
    }

    public function checkDigit(): string
    {
        return $this->value[10];
    }

    public function countryCode(): string
    {
        return \substr($this->value, 11);
    }

    /**
     * The origin as an ISO 3166-1 country; null when the UPU code is not one.
     */
    public function country(): ?CountryCode
    {
        return CountryCode::tryFrom($this->countryCode());
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = TransportRules::compact($value);

        if (\preg_match('/^[A-Z]{2}[0-9]{9}[A-Z]{2}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        if (\preg_match('/^[JKSTW]/', $value) === 1) {
            throw TransportRules::fail(self::TYPE, Reason::RESERVED_SERVICE_INDICATOR);
        }

        if (!S10CheckDigit::isValid(\substr($value, 2, 9))) {
            throw TransportRules::fail(self::TYPE, Reason::WRONG_CHECK_DIGIT);
        }

        return $value;
    }
}
