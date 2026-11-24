<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * World Manufacturer Identifier (ISO 3780): the first three characters of a VIN, digits and capital letters without
 * `I`, `O` and `Q`. {@see region()} decodes the first character by ISO 3780:2009 Table A.1; the country (the second
 * character) is not decoded, because SAE reallocates the country cells and the table is ISO copyright, and the
 * manufacturer names are licensed.
 *
 * @see https://www.law.cornell.edu/cfr/text/49/565.15 49 CFR 565.15(a), (c)
 * @see https://cdn.standards.iteh.ai/samples/45844/6ed6570f8f37401fa4598643cdaae8ac/ISO-3780-2009.pdf ISO 3780:2009 preview, 5.4.2 and Annex A
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "VIN (ISO 3779) and WMI (ISO 3780)"
 */
final class Wmi extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 3;

    private const string TYPE = 'WMI';

    /**
     * The first two characters, which ISO 3780 allocates to a region and a country.
     */
    public function allocation(): string
    {
        return \substr($this->value, 0, 2);
    }

    /**
     * Null for a first character that Table A.1 marks as reserved (`D` to `H`, `7`, `0`); such a WMI is still accepted.
     */
    public function region(): ?WmiRegion
    {
        return match ($this->value[0]) {
            'A', 'B', 'C' => WmiRegion::Africa,
            'J', 'K', 'L', 'M', 'N', 'P', 'R' => WmiRegion::Asia,
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' => WmiRegion::Europe,
            '1', '2', '3', '4', '5' => WmiRegion::NorthAmerica,
            '6' => WmiRegion::Oceania,
            '8', '9' => WmiRegion::SouthAmerica,
            default => null,
        };
    }

    public function manufacturer(): string
    {
        return $this->value[2];
    }

    /**
     * A `9` in the third position marks a manufacturer identified by six characters: positions 12 to 14 of the VIN
     * complete the identifier.
     */
    public function isSmallManufacturer(): bool
    {
        return $this->value[2] === '9';
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('/^[0-9A-HJ-NPR-Z]{3}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
