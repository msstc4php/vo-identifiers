<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Exception\InvalidValueObjectException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;

/**
 * Builds the one identifier type that accepts the value, or throws {@see AmbiguousValueException} listing every type
 * that does. A bare ISNI and an ORCID iD are the same form, so only an ORCID URL (or a label such as `ISNI `) decides
 * between them, and a 16-digit ISAN without check characters is also an ISNI. The factory never picks silently.
 *
 * The candidate classes are frozen for 1.x: a new identifier class joins a factory only in the next major, so a value
 * that resolves to one class keeps resolving to it.
 */
final class PublicationIdentifierFactory
{
    private function __construct()
    {
    }

    public static function fromString(string $value): Isbn|Issn|Ismn|Isrc|Iswc|Isan|Isni|Orcid|Doi|Ror|Ark
    {
        $matches = \array_values(\array_filter([
            Isbn::tryFrom($value),
            Issn::tryFrom($value),
            Ismn::tryFrom($value),
            Isrc::tryFrom($value),
            Iswc::tryFrom($value),
            Isan::tryFrom($value),
            Isni::tryFrom($value),
            Orcid::tryFrom($value),
            Doi::tryFrom($value),
            Ror::tryFrom($value),
            Ark::tryFrom($value),
        ]));

        return match (\count($matches)) {
            0 => throw new InvalidPublicationIdentifierException('Value is not a recognised publication identifier.'),
            1 => $matches[0],
            default => throw new AmbiguousValueException('publication identifier', self::classesOf($matches)),
        };
    }

    /** Null when no candidate or several fit; fromString() says which. */
    public static function tryFromString(string $value): Isbn|Issn|Ismn|Isrc|Iswc|Isan|Isni|Orcid|Doi|Ror|Ark|null
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
