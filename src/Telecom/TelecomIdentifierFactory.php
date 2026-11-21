<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Telecom;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;

/**
 * Builds the one identifier type that accepts the value, or throws {@see AmbiguousValueException} listing every type
 * that does. A 14- or 15-digit number can be an IMEI and an IMSI; a number of at most 15 digits is an IMSI unless it fits
 * another type.
 */
final class TelecomIdentifierFactory
{
    /** @var non-empty-list<class-string<AbstractStringValueObject>> */
    private const array CANDIDATES = [
        Imei::class,
        Imeisv::class,
        Iccid::class,
        Imsi::class,
        Eid::class,
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
