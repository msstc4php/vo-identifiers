<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

// The 0.x name of MmsiStationType, kept for `enumType` mappings and serialized `E:` payloads written by 0.x.
\class_alias(MmsiStationType::class, MmsiType::class);

// False at runtime, as the alias exists by now: the declaration only lets static analysis see the alias.
if (!\enum_exists(MmsiType::class, false)) {
    /**
     * @deprecated since 1.0.0, removed in 2.0; use {@see MmsiStationType}
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
}
