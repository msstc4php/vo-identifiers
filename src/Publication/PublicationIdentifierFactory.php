<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;

/**
 * Builds the one identifier type that accepts the value, or throws {@see AmbiguousValueException} listing every type
 * that does. A bare ISNI and an ORCID iD are the same form, so only an ORCID URL (or a label such as `ISNI `) decides
 * between them; a 979-0 value is both an ISBN-shaped number and an ISMN. The factory never picks silently.
 */
final class PublicationIdentifierFactory
{
    /** @var non-empty-list<class-string<AbstractStringValueObject>> */
    private const array CANDIDATES = [
        Isbn::class,
        Issn::class,
        Ismn::class,
        Isrc::class,
        Iswc::class,
        Isan::class,
        Isni::class,
        Orcid::class,
        Doi::class,
        Ror::class,
        Ark::class,
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
            0 => throw new InvalidPublicationIdentifierException('Value is not a recognised publication identifier.'),
            1 => $matches[0],
            default => throw new AmbiguousValueException('publication identifier', self::classesOf($matches)),
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
