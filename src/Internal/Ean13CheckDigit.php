<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;

/**
 * The check digit of a 13-digit ISBN or ISMN: the first twelve digits are multiplied alternately by 1 and 3, and the
 * check digit is 10 minus the remainder of the sum divided by 10, a result of 10 being 0.
 *
 * @internal
 *
 * @see https://www.isbn-international.org/sites/default/files/ISBN%20International%20Users%20Manual%20-%207th%20edition_absolutely_final.docx ISBN Users' Manual 7th edition, appendix 1, A1.1
 * @see https://ismn-international.org/web_ismn_users_manual_2016/ ISMN Users' Manual 2016, 2.1.4
 */
final class Ean13CheckDigit
{
    /**
     * @return numeric-string one digit
     */
    public static function compute(string $body): string
    {
        if (\strlen($body) !== 12 || \strspn($body, '0123456789') !== 12) {
            throw new LogicException('An EAN-13 body is twelve ASCII digits.');
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (\ord($body[$i]) - 48) * ($i % 2 === 0 ? 1 : 3);
        }

        return (string) ((10 - $sum % 10) % 10);
    }

    /**
     * Whether `$value` is thirteen digits whose last one is the check digit of the first twelve.
     */
    public static function isValid(string $value): bool
    {
        if (\strlen($value) !== 13 || \strspn($value, '0123456789') !== 13) {
            return false;
        }

        return self::compute(\substr($value, 0, 12)) === $value[12];
    }
}
