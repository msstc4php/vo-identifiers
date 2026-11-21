<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;

/**
 * Builds the one identifier type that accepts the value, or throws {@see AmbiguousValueException} listing every type
 * that does. Three letters are both a WMI and an IATA airport code, so such a value is ambiguous. The ISO/IEC 15459
 * identifier is not a candidate: it has no syntax of its own, only the data identifier that carries it. Any seven
 * digits are an IMO number (the `IMO` prefix is optional).
 */
final class TransportIdentifierFactory
{
    public static function fromString(string $value): Vin|Wmi|ContainerNumber|ImoNumber|Mmsi|S10ItemIdentifier|AirWaybillNumber|UicVehicleNumber|UnLocode|IataAirportCode|IcaoAirportCode|DataIdentifierString
    {
        $matches = \array_values(\array_filter([
            Vin::tryFrom($value),
            Wmi::tryFrom($value),
            ContainerNumber::tryFrom($value),
            ImoNumber::tryFrom($value),
            Mmsi::tryFrom($value),
            S10ItemIdentifier::tryFrom($value),
            AirWaybillNumber::tryFrom($value),
            UicVehicleNumber::tryFrom($value),
            UnLocode::tryFrom($value),
            IataAirportCode::tryFrom($value),
            IcaoAirportCode::tryFrom($value),
            DataIdentifierString::tryFrom($value),
        ]));

        return match (\count($matches)) {
            0 => throw new InvalidTransportIdentifierException('Value is not a recognised transport identifier.'),
            1 => $matches[0],
            default => throw new AmbiguousValueException('transport identifier', self::classesOf($matches)),
        };
    }

    /**
     * @param non-empty-list<ValueObject> $matches
     *
     * @return non-empty-list<class-string<ValueObject>>
     */
    private static function classesOf(array $matches): array
    {
        return \array_map(static fn (ValueObject $match): string => $match::class, $matches);
    }
}
