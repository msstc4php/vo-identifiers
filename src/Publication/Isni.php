<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod11_2;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Standard Name Identifier (ISO 27729): fifteen digits and an ISO 7064 MOD 11-2 check character (`X` for
 * ten). Hyphens, spaces and a leading `ISNI ` are dropped; the stored form is the 16 compact characters. An ORCID iD is
 * the same form: see {@see Orcid}.
 *
 * @see https://standards.iteh.ai/catalog/standards/iso/6ba7c022-bf45-4860-a066-ff57650ab09e/iso-27729-2012 ISO 27729:2012, clause 4 and annex A
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "ISNI (ISO 27729) and ORCID"
 */
final class Isni extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 16;

    private const string TYPE = 'ISNI';

    /**
     * @return non-empty-string `ISNI XXXX XXXX XXXX XXXX`
     */
    public function formatted(): string
    {
        return 'ISNI ' . \implode(' ', \str_split($this->value, 4));
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = PublicationRules::compact($value, 'ISNI');

        if (\preg_match('/^[0-9]{15}[0-9X]$/D', $value) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        if (!Iso7064Mod11_2::isValid($value)) {
            throw PublicationRules::fail(self::TYPE, Reason::WRONG_CHECK_CHARACTER);
        }

        return $value;
    }
}
