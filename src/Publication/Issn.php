<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Gs1\Key\Gtin;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Ean13CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Mod11CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Identifiers\Internal\ReparsesOnUnserialize;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Standard Serial Number (ISO 3297): seven digits and a modulus 11 check character (`X` for ten), printed
 * `NNNN-NNNC` and stored that way. A value typed without its check character (seven digits) is accepted and completed.
 * Hyphens, spaces and a leading `ISSN ` are dropped.
 *
 * @see https://www.issn.org/wp-content/uploads/2025/05/Manual-ISSN_ENG-marc21_May2025.pdf ISSN Manual (December 2024), 5.1.1
 * @see https://www.issn.org/fr/comprendre-lissn/politique-dattribution/lidentification-avec-le-code-a-barres-ean-13/ ISSN International Centre, the 977 EAN-13
 * @see docs/sources/IDENTIFIERS.md "ISSN (ISO 3297)"
 */
final class Issn extends AbstractStringValueObject
{
    use ReparsesOnUnserialize;

    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 9;

    private const string TYPE = 'ISSN';

    /**
     * The 977 GTIN-13: `977`, the seven ISSN digits, the two-digit variant (`00` for a normal title) and the EAN check
     * digit.
     *
     * @throws InvalidArgumentException when the variant is not two digits (a caller error, not an invalid ISSN)
     */
    public function toGtin(string $variant = '00'): Gtin
    {
        if (\preg_match('/^[0-9]{2}$/D', $variant) !== 1) {
            throw new InvalidArgumentException('ISSN GTIN variant must be two digits.');
        }

        $body = '977' . \substr($this->value, 0, 4) . \substr($this->value, 5, 3) . $variant;

        return new Gtin($body . Ean13CheckDigit::compute($body));
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = PublicationRules::compact($value, 'ISSN');

        if (\preg_match('/^[0-9]{7}$/D', $value) === 1) {
            $value .= Mod11CheckDigit::compute($value);
        } elseif (\preg_match('/^[0-9]{7}[0-9X]$/D', $value) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        } elseif (!Mod11CheckDigit::isValid($value)) {
            throw PublicationRules::fail(self::TYPE, Reason::WRONG_CHECK_DIGIT);
        }

        return \substr($value, 0, 4) . '-' . \substr($value, 4);
    }
}
