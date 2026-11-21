<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Substance;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\EcNumberCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Identifiers\Internal\ReparsesOnUnserialize;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * ECHA EC or list number: six digits (list numbers may carry capital letters other than I, L, O) and a check digit,
 * stored as `NNN-NNN-N`. The hyphens are optional on input, all or none.
 *
 * @see https://echa.europa.eu/about-ec-and-list-numbers ECHA, "About EC and list numbers"
 * @see docs/sources/IDENTIFIERS.md "EcNumber"
 */
final class EcNumber extends AbstractStringValueObject
{
    use ReparsesOnUnserialize;

    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 9;

    private const string TYPE = 'EC number';

    public function checkDigit(): string
    {
        return \substr($this->value, -1);
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('/^[0-9A-HJKMNP-Z]{3}(-?)[0-9A-HJKMNP-Z]{3}\1[0-9]$/D', $value) !== 1) {
            throw CommodityRules::substance(self::TYPE);
        }

        $compact = \str_replace('-', '', $value);

        if (!EcNumberCheckDigit::isValid($compact)) {
            throw CommodityRules::substance(self::TYPE, Reason::WRONG_CHECK_DIGIT);
        }

        $first = \substr($compact, 0, 3);
        $second = \substr($compact, 3, 3);
        $check = \substr($compact, 6);

        return $first . '-' . $second . '-' . $check;
    }
}
