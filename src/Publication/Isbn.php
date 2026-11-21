<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Gs1\Key\Gtin;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Ean13CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Mod11CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Identifiers\Internal\ReparsesOnUnserialize;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Standard Book Number (ISO 2108): ISBN-13 (prefix 978 or 979, EAN check digit) or the older ISBN-10
 * (modulus 11, `X` for ten). Hyphens, spaces and a leading `ISBN`, `ISBN-13` or `ISBN-10` label are dropped; the stored form is compact and keeps the
 * length given, so an ISBN-10 stays an ISBN-10. {@see equals()} compares the ISBN-13 forms, so both forms of one book
 * are equal. A 979-0 value is an ISMN, not an ISBN (ISMN Manual 2.1.1) and is refused here.
 *
 * Hyphen placement needs the International ISBN Agency's range table, whose terms forbid republishing it, so there is no
 * formatted form.
 *
 * @see https://www.isbn-international.org/sites/default/files/ISBN%20International%20Users%20Manual%20-%207th%20edition_absolutely_final.docx ISBN Users' Manual 7th edition, 4.1 and appendix 1
 * @see http://web.archive.org/web/20051228025615/http://www.isbn-international.org:80/en/userman/chapter4.html ISBN Users' Manual online, 4.4 (ISBN-10 check digit, Internet Archive copy)
 * @see docs/sources/IDENTIFIERS.md "ISBN (ISO 2108)"
 */
final class Isbn extends AbstractStringValueObject
{
    use ReparsesOnUnserialize;

    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 13;

    private const string TYPE = 'ISBN';

    public function isIsbn10(): bool
    {
        return \strlen($this->value) === 10;
    }

    /**
     * Thirteen digits.
     */
    public function isbn13(): string
    {
        if (!$this->isIsbn10()) {
            return $this->value;
        }

        $body = '978' . \substr($this->value, 0, 9);

        return $body . Ean13CheckDigit::compute($body);
    }

    /**
     * Null for a 979 ISBN, which has no ISBN-10.
     */
    public function isbn10(): ?string
    {
        if ($this->isIsbn10()) {
            return $this->value;
        }

        if (!\str_starts_with($this->value, '978')) {
            return null;
        }

        $body = \substr($this->value, 3, 9);

        return $body . Mod11CheckDigit::compute($body);
    }

    public function toGtin(): Gtin
    {
        return new Gtin($this->isbn13());
    }

    #[Override]
    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $other->isbn13() === $this->isbn13();
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = PublicationRules::compact($value, 'ISBN(?:-1[03])?');

        if (\preg_match('/^[0-9]{9}[0-9X]$/D', $value) === 1) {
            if (!Mod11CheckDigit::isValid($value)) {
                throw PublicationRules::fail(self::TYPE, Reason::WRONG_CHECK_DIGIT);
            }

            return $value;
        }

        if (\preg_match('/^97(?:8[0-9]|9[1-9])[0-9]{9}$/D', $value) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        if (!Ean13CheckDigit::isValid($value)) {
            throw PublicationRules::fail(self::TYPE, Reason::WRONG_CHECK_DIGIT);
        }

        return $value;
    }
}
