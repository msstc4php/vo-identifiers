<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Telecom;

use MSSTC4PHP\ValueObject\Contract\Maskable;
use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Mobile station Equipment Identity: a Type Allocation Code of 8 digits, a serial number of 6 digits and a
 * Luhn check digit. The check digit is not transmitted over the air, so 14 digits are a valid IMEI; with 15 digits the
 * check digit must be right. Spaces and hyphens are dropped. The first 8 digits stay visible in {@see masked()}.
 *
 * @see https://www.etsi.org/deliver/etsi_ts/123000_123099/123003/17.07.00_60/ts_123003v170700p.pdf 3GPP TS 23.003 V17.7.0, 6.2.1 and annex B
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "Imei, Imeisv, Imsi"
 */
final class Imei extends AbstractStringValueObject implements Maskable
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 15;

    private const string TYPE = 'IMEI';

    /**
     * The 14-digit and the 15-digit form of one IMEI are equal: the check digit is derived and not always transmitted.
     */
    #[Override]
    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $other->base() === $this->base();
    }

    public function tac(): string
    {
        return \substr($this->value, 0, 8);
    }

    public function serialNumber(): string
    {
        return \substr($this->value, 8, 6);
    }

    public function hasCheckDigit(): bool
    {
        return \strlen($this->value) === 15;
    }

    /**
     * The check digit written in the value, or the one that completes the 14 digits.
     *
     * @return numeric-string one digit
     */
    public function checkDigit(): string
    {
        return Luhn::compute($this->base());
    }

    public function withCheckDigit(): self
    {
        return $this->hasCheckDigit() ? $this : new self($this->value . $this->checkDigit());
    }

    #[Override]
    public function masked(): string
    {
        return TelecomRules::mask($this->value, 8, 2);
    }

    #[Override]
    protected function parse(string $value): string
    {
        $digits = \str_replace([' ', '-'], '', $value);
        if (\preg_match('/^[0-9]{14,15}$/D', $digits) !== 1) {
            throw TelecomRules::invalidFormat(self::TYPE);
        }
        if (\strlen($digits) === 15 && !Luhn::isValid($digits)) {
            throw TelecomRules::invalidCheckDigit(self::TYPE);
        }

        return $digits;
    }

    private function base(): string
    {
        return \substr($this->value, 0, 14);
    }
}
