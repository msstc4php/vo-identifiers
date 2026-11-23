<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Digital Object Identifier (ISO 26324): a numeric directory indicator (usually `10`), a full stop, a registrant
 * code of digits and full stops, a slash, and a suffix of any printable Unicode characters, so the suffix may itself
 * hold slashes. Accepted bare, `doi:`-prefixed (one space may follow the colon) or as a resolver URL on `doi.org`,
 * `dx.doi.org` (deprecated by the Handbook, still resolving) and their `www.` forms (accepted since 0.1): the URL's
 * query and fragment (from a raw `?` or `#`) are dropped and the rest is percent-decoded (RFC 3986), so `%3F` gives a
 * `?` of the DOI. When the part before the raw `?` or `#` is no DOI, the whole rest is decoded instead, as 0.x did.
 * Stored bare and decoded, with the case as given. The Handbook calls a DOI case-insensitive without fixing the
 * definition, so {@see equals()} folds ASCII case only. Leading or trailing whitespace and control characters are
 * refused.
 *
 * @see https://www.doi.org/doi-handbook/HTML/ DOI Handbook, syntax specification
 * @see https://www.doi.org/doi-handbook/DOIHandbook_2025.pdf DOI Handbook 2025, 4.4.4 (HTTP proxy form), 4.7 (percent-encoding), 6.3.1 (DOI proxy)
 * @see https://www.doi.org/the-identifier/resources/factsheets/doi-resolution-documentation DOI resolution documentation, 2 (mandatory encoding of `?` and `#`)
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "DOI (ISO 26324)"
 */
final class Doi extends AbstractStringValueObject
{
    private const string TYPE = 'DOI';

    /**
     * `10.` and the registrant code.
     */
    public function prefix(): string
    {
        return \explode('/', $this->value, 2)[0];
    }

    public function suffix(): string
    {
        return \explode('/', $this->value, 2)[1];
    }

    #[Override]
    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && \strtolower($other->value) === \strtolower($this->value);
    }

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('~^https?://(?:www\.)?(?:dx\.)?doi\.org/(([^?#]*).*)~is', $value, $match) === 1) {
            // A raw `?` or `#` starts the URL's query or fragment; one inside the DOI is percent-encoded (RFC 3986 3.4, 3.5).
            $withoutQuery = \rawurldecode($match[2]);
            $value = $this->isDoi($withoutQuery) ? $withoutQuery : \rawurldecode($match[1]);
        } elseif (\preg_match('~^doi: ?~i', $value, $match) === 1) {
            $value = \substr($value, \strlen($match[0]));
        }

        if (!$this->isDoi($value)) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        return $value;
    }

    private function isDoi(string $value): bool
    {
        return \preg_match('~^[0-9]+(?:\.[0-9]+)+/[^\p{C}\s](?:\P{C}*[^\p{C}\s])?$~Du', $value) === 1;
    }
}
