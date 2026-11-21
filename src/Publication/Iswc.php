<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\ReparsesOnUnserialize;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Standard Musical Work Code (ISO 15707): the letter `T`, nine digits and a check digit. The check digit
 * is not enforced: its algorithm (Annex B of the standard) is not reachable. Hyphens, dots, spaces and a leading
 * `ISWC ` are dropped; the stored form is compact.
 *
 * @see https://cisaciswcuat.developer.azure-api.net/content/SPE_20190806_ISWC_EDI_FileFormat.pdf CISAC ISWC business rules (ISO 15707:2022 clause 4 as quoted in the source notes)
 * @see docs/sources/IDENTIFIERS.md "ISWC (ISO 15707)"
 */
final class Iswc extends AbstractStringValueObject
{
    use ReparsesOnUnserialize;

    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 11;

    private const string TYPE = 'ISWC';

    /**
     * @return non-empty-string `T-NNN.NNN.NNN-C`
     */
    public function formatted(): string
    {
        return \sprintf(
            'T-%s.%s.%s-%s',
            \substr($this->value, 1, 3),
            \substr($this->value, 4, 3),
            \substr($this->value, 7, 3),
            \substr($this->value, 10),
        );
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = PublicationRules::compact($value, 'ISWC', '-. ');

        if (\preg_match('/^T[0-9]{10}$/D', $value) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
