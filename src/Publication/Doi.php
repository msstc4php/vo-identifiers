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
 * hold slashes. Accepted bare, `doi:`-prefixed or as a `doi.org` resolver URL (`www.` and `dx.` hosts too), whose
 * percent-encoding is decoded (RFC 3986); stored bare and decoded, with the case as given. The Handbook calls a DOI case-insensitive without
 * fixing the definition, so {@see equals()} folds ASCII case only. Leading or trailing whitespace and control characters
 * are refused.
 *
 * @see https://www.doi.org/doi-handbook/HTML/ DOI Handbook, syntax specification
 * @see docs/sources/IDENTIFIERS.md "DOI (ISO 26324)"
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
        if (\preg_match('~^https?://(?:www\.)?(?:dx\.)?doi\.org/~i', $value, $match) === 1) {
            $value = \rawurldecode(\substr($value, \strlen($match[0])));
        } elseif (\preg_match('~^doi:~i', $value) === 1) {
            $value = \substr($value, 4);
        }

        $pattern = '~^[0-9]+(?:\.[0-9]+)+/[^\p{C}\s](?:\P{C}*[^\p{C}\s])?$~Du';
        if (\preg_match($pattern, $value) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
