<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Telecom;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Exception\InvalidValueObjectException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;

/**
 * Builds the one identifier type that accepts the value, or throws {@see AmbiguousValueException} listing every type
 * that does. Bare 14- or 15-digit input is always ambiguous: it is an IMEI and an IMSI, and only an IMEI written with
 * spaces or hyphens resolves (an IMSI takes digits only). A short number starting `89` with a valid Luhn digit can be an
 * ICCID, an IMEI and an IMSI. No heuristic picks one: when the field's kind is known, use the class itself.
 *
 * The candidate classes are frozen for 1.x: a new identifier class joins a factory only in the next major, so a value
 * that resolves to one class keeps resolving to it.
 */
final class TelecomIdentifierFactory
{
    private function __construct()
    {
    }

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

    /** Null when no candidate or several fit; fromString() says which. */
    public static function tryFromString(string $value): Imei|Imeisv|Iccid|Imsi|Eid|null
    {
        try {
            return self::fromString($value);
        } catch (InvalidValueObjectException) {
            return null;
        }
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
