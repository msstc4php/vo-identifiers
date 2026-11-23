<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Telecom;

use MSSTC4PHP\ValueObject\Contract\Maskable;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * IMEI and Software Version: a Type Allocation Code of 8 digits, a serial number of 6 digits and a Software Version
 * Number of 2 digits; there is no check digit. Spaces and hyphens are dropped.
 *
 * @see https://www.etsi.org/deliver/etsi_ts/123000_123099/123003/17.07.00_60/ts_123003v170700p.pdf 3GPP TS 23.003 V17.7.0, 6.2.2
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "Imei, Imeisv, Imsi"
 */
final class Imeisv extends AbstractStringValueObject implements Maskable
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 16;

    private const string TYPE = 'IMEISV';

    public function tac(): string
    {
        return \substr($this->value, 0, 8);
    }

    public function serialNumber(): string
    {
        return \substr($this->value, 8, 6);
    }

    public function softwareVersion(): string
    {
        return \substr($this->value, -2);
    }

    /**
     * The IMEI without its check digit; {@see Imei::withCheckDigit()} completes it.
     */
    public function toImei(): Imei
    {
        return new Imei(\substr($this->value, 0, 14));
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
        if (\preg_match('/^[0-9]{16}$/D', $digits) !== 1) {
            throw TelecomRules::invalidFormat(self::TYPE);
        }

        return $digits;
    }
}
