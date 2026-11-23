<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Energy;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\EicCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * ENTSO-E Energy Identification Code: 16 characters of A-Z, 0-9 and `-`, being a 2-character issuing office, an object
 * type character, 12 characters and a check character. Only the validity of an allocated code is checked.
 *
 * @see https://eepublicdownloads.entsoe.eu/clean-documents/EDI/Library/EIC_Short_Guide_and_FAQ_V3_Approved%20April%202016.pdf ENTSO-E, EIC Short Guide and FAQ V3
 * @see https://eepublicdownloads.entsoe.eu/clean-documents/EDI/Library/cim_based/EIC_Data_Exchange_IG_v1.2.pdf ENTSO-E, EIC Data Exchange Implementation Guide v1.2
 * @see docs/sources/IDENTIFIERS.md "EnergyIdentificationCode (ENTSO-E)"
 */
final class EnergyIdentificationCode extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 16;

    private const string TYPE = 'Energy Identification Code';

    public function issuingOffice(): string
    {
        return \substr($this->value, 0, 2);
    }

    public function objectTypeCode(): string
    {
        return $this->value[2];
    }

    /**
     * @return EicObjectType|null null for a letter outside the Short Guide's list; such a code is still accepted
     */
    public function objectType(): ?EicObjectType
    {
        return EicObjectType::tryFrom($this->objectTypeCode());
    }

    public function checkCharacter(): string
    {
        return $this->value[15];
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('/^[0-9A-Z-]{15}[0-9A-Z]$/D', $value) !== 1) {
            throw CommodityRules::energy(self::TYPE);
        }

        if (!EicCheckDigit::isValid($value)) {
            throw CommodityRules::energy(self::TYPE, Reason::WRONG_CHECK_CHARACTER);
        }

        return $value;
    }
}
