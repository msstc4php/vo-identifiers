<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

/**
 * The geographic zone ISO 3780:2009 Annex A, Table A.1 allocates to the first character of a WMI. The characters the
 * table marks as reserved (`D` to `H`, `7`, `0`) have no case.
 *
 * Cases may be added in a minor release, so a `match` over the cases needs a default arm; the backed values never change.
 *
 * @see https://cdn.standards.iteh.ai/samples/45844/6ed6570f8f37401fa4598643cdaae8ac/ISO-3780-2009.pdf ISO 3780:2009 preview, Annex A (normative)
 */
enum WmiRegion: string
{
    case Africa = 'africa';
    case Asia = 'asia';
    case Europe = 'europe';
    case NorthAmerica = 'north-america';
    case Oceania = 'oceania';
    case SouthAmerica = 'south-america';
}
