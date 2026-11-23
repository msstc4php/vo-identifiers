<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Identifiers\Internal\RorCheckDigits;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Research Organization Registry identifier: `0`, six Crockford base32 characters (no I, L, O, U) and two check digits
 * (ISO/IEC 7064 MOD 97-10 over the decoded value). Accepted bare or as `https://ror.org/...`; stored bare and lowercase.
 *
 * @see https://ror.readme.io/docs/identifier ROR, "Identifier pattern"
 * @see docs/sources/IDENTIFIERS.md "ROR"
 */
final class Ror extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 9;

    private const string TYPE = 'ROR';

    /**
     * @return non-empty-string
     */
    public function uri(): string
    {
        return 'https://ror.org/' . $this->value;
    }

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('~^(?:(?:https?://)?(?:www\.)?ror\.org/)?(0[0-9a-hjkmnp-tv-z]{6}[0-9]{2})$~Di', $value, $match) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        $value = \strtolower($match[1]);

        if (!RorCheckDigits::isValid(\substr($value, 1))) {
            throw PublicationRules::fail(self::TYPE, Reason::WRONG_CHECK_DIGIT);
        }

        return $value;
    }
}
