<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The modulus 11 check digit of an ISSN and of an ISBN-10: the digits of the body are multiplied by weights that count
 * down to 2 (8 to 2 for a seven-digit ISSN body, 10 to 2 for a nine-digit ISBN body), and the check digit is 11 minus
 * the remainder of the sum divided by 11, a result of 11 being 0 and a result of 10 being `X`.
 *
 * @internal
 *
 * @see https://www.issn.org/wp-content/uploads/2025/05/Manual-ISSN_ENG-marc21_May2025.pdf ISSN Manual (December 2024), 5.1.1
 * @see http://web.archive.org/web/20051228025615/http://www.isbn-international.org:80/en/userman/chapter4.html International ISBN Agency, ISBN Users' Manual online, 4.4 (Internet Archive copy)
 */
final class Mod11CheckDigit
{
    /**
     * @return non-empty-string one digit or `X`
     */
    public static function compute(string $body): string
    {
        $length = \strlen($body);
        if ($length === 0 || \strspn($body, '0123456789') !== $length) {
            throw new LogicException('A modulus 11 body is ASCII digits.');
        }

        $sum = 0;
        for ($i = 0; $i < $length; $i++) {
            $sum += (\ord($body[$i]) - 48) * ($length + 1 - $i);
        }

        $check = (11 - $sum % 11) % 11;

        return $check === 10 ? 'X' : (string) $check;
    }

    /**
     * Whether the last character of `$value` is the check digit of the digits before it.
     */
    public static function isValid(string $value): bool
    {
        $length = \strlen($value);
        if ($length < 2 || \strspn($value, '0123456789', 0, $length - 1) !== $length - 1) {
            return false;
        }

        return self::compute(\substr($value, 0, -1)) === $value[$length - 1];
    }
}
