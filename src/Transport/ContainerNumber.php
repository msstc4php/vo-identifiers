<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\ReparsesOnUnserialize;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Freight container number (ISO 6346, BIC): a three-letter owner code, the equipment category (`U` freight container,
 * `J` detachable equipment, `Z` trailer or chassis), six serial digits and a check digit. Spaces are dropped and the
 * stored form is compact. The check digit is not verified: the ISO 6346 algorithm is not quoted from any reachable
 * official text.
 *
 * @see https://www.bic-code.org/bic-codes/ BIC, the structure of a container code
 * @see https://www.bic-code.org/check-digit-calculator/ BIC, the equipment category letters
 * @see docs/sources/IDENTIFIERS.md "ContainerNumber (ISO 6346, BIC)"
 */
final class ContainerNumber extends AbstractStringValueObject
{
    use ReparsesOnUnserialize;

    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 11;

    private const string TYPE = 'Container number';

    public function ownerCode(): string
    {
        return \substr($this->value, 0, 3);
    }

    public function category(): string
    {
        return $this->value[3];
    }

    public function serialNumber(): string
    {
        return \substr($this->value, 4, 6);
    }

    public function checkDigit(): string
    {
        return $this->value[10];
    }

    /**
     * @return non-empty-string `BICU 123456 5`
     */
    public function formatted(): string
    {
        return \substr($this->value, 0, 4) . ' ' . $this->serialNumber() . ' ' . $this->checkDigit();
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = TransportRules::compact($value);

        if (\preg_match('/^[A-Z]{3}[UJZ][0-9]{7}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
