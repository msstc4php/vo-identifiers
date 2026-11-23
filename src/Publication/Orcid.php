<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod11_2;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * ORCID iD: an ISNI-compatible identifier with the same ISO 7064 MOD 11-2 check character, printed with a hyphen after
 * every four characters and stored that way. Accepted bare, hyphenated or as `https://orcid.org/...` (also `http`, `www`
 * and the scheme-less `orcid.org/...`). The assigned ORCID blocks are not enforced: they are an allocation, not part of the syntax.
 *
 * @see https://support.orcid.org/hc/en-us/articles/360006897674 ORCID, "Structure of the ORCID Identifier"
 * @see docs/sources/IDENTIFIERS.md "ISNI (ISO 27729) and ORCID"
 */
final class Orcid extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 19;

    private const string TYPE = 'ORCID';

    /**
     * @return non-empty-string
     */
    public function uri(): string
    {
        return 'https://orcid.org/' . $this->value;
    }

    public function toIsni(): Isni
    {
        return new Isni($this->value);
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper(\preg_replace('~^(?:https?://)?(?:www\.)?orcid\.org/~i', '', $value) ?? $value);

        if (\preg_match('/^[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{3}[0-9X]$/D', $value) === 1) {
            $value = \str_replace('-', '', $value);
        }

        if (\preg_match('/^[0-9]{15}[0-9X]$/D', $value) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        if (!Iso7064Mod11_2::isValid($value)) {
            throw PublicationRules::fail(self::TYPE, Reason::WRONG_CHECK_CHARACTER);
        }

        return \implode('-', \str_split($value, 4));
    }
}
