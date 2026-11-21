<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

/**
 * The kind of station an MMSI identifies, from its leading digits (ITU-R M.585, Annexes 1 and 2).
 *
 * @see https://www.itu.int/dms_pubrec/itu-r/rec/m/R-REC-M.585-10-202604-I!!PDF-E.pdf ITU-R M.585-10, Annexes 1 and 2
 * @see docs/sources/IDENTIFIERS.md "Mmsi"
 */
enum MmsiType: string
{
    case Ship = 'ship';
    case GroupShip = 'group_ship';
    case CoastStation = 'coast_station';
    case SarAircraft = 'sar_aircraft';
    case AidToNavigation = 'aid_to_navigation';
    case CraftAssociated = 'craft_associated';
    case DistressDevice = 'distress_device';
    case Unclassified = 'unclassified';
}
