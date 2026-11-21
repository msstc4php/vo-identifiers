<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Archival Resource Key: `ark:`, a NAAN of betanumeric characters, a slash and a Name (with any qualifiers). Format only:
 * the check character is optional in the specification, so it is not enforced. The old `ark:/` label and a leading
 * name-mapping-authority URL are accepted; the stored form is `ark:NAAN/Name` with the label and NAAN lowercased and the
 * Name as given (it is case-sensitive; hyphens in it are kept although the specification says they are insignificant).
 *
 * @see https://datatracker.ietf.org/doc/html/draft-kunze-ark-40 The ARK Identifier Scheme (draft-kunze-ark-40)
 * @see docs/sources/IDENTIFIERS.md "ARK"
 */
final class Ark extends AbstractStringValueObject
{
    private const string TYPE = 'ARK';

    public function naan(): string
    {
        return \explode('/', \substr($this->value, 4), 2)[0];
    }

    public function name(): string
    {
        return \explode('/', $this->value, 2)[1];
    }

    #[Override]
    protected function parse(string $value): string
    {
        $pattern = '~^(?:https?://[^/\s]+/)?ark:/?([bcdfghjkmnpqrstvwxz0-9]+)/([^\p{C}\s]+)$~Diu';
        if (\preg_match($pattern, $value, $match) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        return 'ark:' . \strtolower($match[1]) . '/' . $match[2];
    }
}
