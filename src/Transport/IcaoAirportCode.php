<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * ICAO four-letter location indicator, uppercased. Format only: Doc 7910 is sold by ICAO and its nationality letters are
 * not decoded.
 *
 * @see https://www.icao.int/operational-safety/Designators-and-indicators ICAO, Doc 7910 Location Indicators
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "UnLocode, IATA, ICAO"
 */
final class IcaoAirportCode extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 4;

    private const string TYPE = 'ICAO airport code';

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('/^[A-Z]{4}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
