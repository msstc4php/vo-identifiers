<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Gs1\Key\Gtin;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Ean13CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Standard Music Number (ISO 10957): `979-0` and nine digits, the last an EAN check digit. The old ten
 * character form `M` and nine digits is accepted and stored as `979-0...`, as the ISMN Manual says to convert it (the
 * check digit is kept). Hyphens, spaces and a leading `ISMN ` are dropped; the stored form is the 13 digits.
 *
 * @see https://ismn-international.org/web_ismn_users_manual_2016/ ISMN Users' Manual 2016, 2, 2.1.1 and 10
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "ISMN (ISO 10957)"
 */
final class Ismn extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 13;

    private const string TYPE = 'ISMN';

    public function toGtin(): Gtin
    {
        return new Gtin($this->value);
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = PublicationRules::compact($value, 'ISMN');

        if (\preg_match('/^M[0-9]{9}$/D', $value) === 1) {
            $value = '9790' . \substr($value, 1);
        }

        if (\preg_match('/^9790[0-9]{9}$/D', $value) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        if (!Ean13CheckDigit::isValid($value)) {
            throw PublicationRules::fail(self::TYPE, Reason::WRONG_CHECK_DIGIT);
        }

        return $value;
    }
}
