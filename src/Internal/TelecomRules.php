<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;

/**
 * Messages name the identifier and the reason, never the input or any part of it.
 *
 * @internal
 */
final class TelecomRules
{
    private const int MIN_MASKED = 6;

    /**
     * @param non-empty-string $type
     */
    public static function invalidFormat(string $type): InvalidTelecomIdentifierException
    {
        return new InvalidTelecomIdentifierException(\sprintf('%s has an invalid format.', $type));
    }

    /**
     * @param non-empty-string $type
     */
    public static function invalidCheckDigit(string $type): InvalidTelecomIdentifierException
    {
        return new InvalidTelecomIdentifierException(\sprintf('%s has an invalid check digit.', $type));
    }

    /**
     * Keeps the first `$head` characters and the last `$tail` ones only when at least six characters stay masked;
     * otherwise only the first `$head`. The rest are asterisks and the length is kept. The caller's `$head` leaves at
     * least one character.
     *
     * @param int<0, max> $head
     * @param int<0, max> $tail
     *
     * @return non-empty-string
     */
    public static function mask(string $value, int $head, int $tail): string
    {
        $length = \strlen($value);
        $keptTail = $length - $head - $tail >= self::MIN_MASKED ? $tail : 0;

        return \substr($value, 0, $head) . '*' . \str_repeat('*', $length - $head - $keptTail - 1) . \substr($value, $length - $keptTail);
    }
}
