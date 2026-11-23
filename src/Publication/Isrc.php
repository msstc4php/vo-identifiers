<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Standard Recording Code (ISO 3901): twelve characters, two letters, three letters or digits, two digits
 * (the year) and five digits, with no check digit. Hyphens, spaces and a leading `ISRC ` are dropped and the letters
 * uppercased; the stored form is compact. The first five characters are the prefix code the registrant was given; the
 * two letters are not checked against ISO 3166: new prefixes are allocated as one five-character block, and the agency
 * code list allocates codes such as `QM` (United States) and `ZZ` (worldwide) that are no country code.
 *
 * @see https://isrc.ifpi.org/isrc-standard/isrc-structure IFPI, ISRC structure
 * @see https://isrc.ifpi.org/images/downloads/ISRC_Handbook.pdf ISRC Handbook, 4th edition 2021, 3.3.2 (prefix code)
 * @see https://isrc.ifpi.org/images/downloads/Valid-Characters-in-the-ISRC-Prefix.pdf ISRC Agency Code Allocations
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "ISRC (ISO 3901)"
 */
final class Isrc extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 12;

    private const string TYPE = 'ISRC';

    /**
     * The two letters earlier editions of ISO 3901 called the country code; not checked against ISO 3166, as some
     * allocated codes are no country code.
     */
    public function countryCode(): string
    {
        return $this->part(0, 2);
    }

    public function registrant(): string
    {
        return $this->part(2, 3);
    }

    public function year(): string
    {
        return $this->part(5, 2);
    }

    public function designation(): string
    {
        return $this->part(7, 5);
    }

    /**
     * @return non-empty-string `CC-XXX-YY-NNNNN`
     */
    public function formatted(): string
    {
        return \implode('-', [$this->countryCode(), $this->registrant(), $this->year(), $this->designation()]);
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = PublicationRules::compact($value, 'ISRC');

        if (\preg_match('/^[A-Z]{2}[A-Z0-9]{3}[0-9]{7}$/D', $value) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        return $value;
    }

    private function part(int $offset, int $length): string
    {
        return \substr($this->value, $offset, $length);
    }
}
