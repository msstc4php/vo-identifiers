<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\ReparsesOnUnserialize;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * IATA three-letter location identifier, uppercased. Format only: the list is licensed and not embedded.
 *
 * @see https://www.icao.int/operational-safety/Designators-and-indicators ICAO, Doc 7910 and the IATA location identifiers
 * @see docs/sources/IDENTIFIERS.md "UnLocode, IATA, ICAO"
 */
final class IataAirportCode extends AbstractStringValueObject
{
    use ReparsesOnUnserialize;

    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 3;

    private const string TYPE = 'IATA airport code';

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('/^[A-Z]{3}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
