<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Telecom;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;

/**
 * Builds the one identifier type that accepts the value, or throws {@see AmbiguousValueException} listing every type
 * that does. A 14- or 15-digit number can be an IMEI and an IMSI, and a short number starting `89` with a valid Luhn
 * digit can be an ICCID, an IMEI and an IMSI.
 */
final class TelecomIdentifierFactory
{
    public static function fromString(string $value): Imei|Imeisv|Iccid|Imsi|Eid
    {
        $matches = \array_values(\array_filter([
            Imei::tryFrom($value),
            Imeisv::tryFrom($value),
            Iccid::tryFrom($value),
            Imsi::tryFrom($value),
            Eid::tryFrom($value),
        ]));

        return match (\count($matches)) {
            0 => throw new InvalidTelecomIdentifierException('Value is not a recognised telecom identifier.'),
            1 => $matches[0],
            default => throw new AmbiguousValueException('telecom identifier', self::classesOf($matches)),
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
