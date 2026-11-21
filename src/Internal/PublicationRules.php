<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;

/**
 * Messages name the identifier and the reason, never the input or any part of it.
 *
 * @internal
 */
final class PublicationRules
{
    /**
     * @param non-empty-string $type
     */
    public static function fail(string $type, string $reason): InvalidPublicationIdentifierException
    {
        return new InvalidPublicationIdentifierException(\sprintf('%s %s.', $type, $reason));
    }

    /**
     * @param non-empty-string $type
     */
    public static function invalidFormat(string $type): InvalidPublicationIdentifierException
    {
        return self::fail($type, 'has an invalid format');
    }

    /**
     * Drops the printed label (`ISBN `, `ISBN-13: `) and the separators the source prints, and uppercases. The label is
     * only a label when a colon or a space follows it, so a code that happens to start with the letters survives.
     *
     * @param non-empty-string $label PCRE fragment, matched case-insensitively
     * @param non-empty-string $separators each character is a separator
     */
    public static function compact(string $value, string $label, string $separators = '- '): string
    {
        if (\preg_match('/^' . $label . '(?::[ ]*|[ ]+)/Di', $value, $match) === 1) {
            $value = \substr($value, \strlen($match[0]));
        }

        return \strtoupper(\str_replace(\str_split($separators), '', $value));
    }
}
