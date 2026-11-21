<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Energy;

/**
 * The object types an EIC code can identify, by the letter in its third position.
 *
 * @see https://eepublicdownloads.entsoe.eu/clean-documents/EDI/Library/EIC_Short_Guide_and_FAQ_V3_Approved%20April%202016.pdf ENTSO-E, EIC Short Guide and FAQ V3
 */
enum EicObjectType: string
{
    case Party = 'X';
    case Area = 'Y';
    case MeasuringPoint = 'Z';
    case ResourceObject = 'W';
    case TieLine = 'T';
    case Location = 'V';
    case Substation = 'A';
}
