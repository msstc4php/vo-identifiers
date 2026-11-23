<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * World Manufacturer Identifier (ISO 3780): the first three characters of a VIN, digits and capital letters without
 * `I`, `O` and `Q`. The country and region allocation and the manufacturer names are not decoded: no official table was
 * reachable and the manufacturer list is licensed.
 *
 * @see https://www.law.cornell.edu/cfr/text/49/565.15 49 CFR 565.15(a), (c)
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
