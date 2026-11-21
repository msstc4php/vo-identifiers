<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;

/**
 * Builds the one identifier type that accepts the value, or throws {@see AmbiguousValueException} listing every type
 * that does. Three letters are both a WMI and an IATA airport code, so such a value is ambiguous. The ISO/IEC 15459
 * identifier is not a candidate: it has no syntax of its own, only the data identifier that carries it.
 */
final class TransportIdentifierFactory
{
    /** @var non-empty-list<class-string<AbstractStringValueObject>> */
    private const array CANDIDATES = [
        Vin::class,
        Wmi::class,
        ContainerNumber::class,
        ImoNumber::class,
        Mmsi::class,
        S10ItemIdentifier::class,
        AirWaybillNumber::class,
        UicVehicleNumber::class,
        UnLocode::class,
        IataAirportCode::class,
        IcaoAirportCode::class,
        DataIdentifierString::class,
    ];

    public static function fromString(string $value): ValueObject
    {
        $matches = [];
        foreach (self::CANDIDATES as $class) {
            $candidate = $class::tryFrom($value);
            if ($candidate !== null) {
                $matches[] = $candidate;
            }
        }

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
