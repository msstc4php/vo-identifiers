<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Telecom;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Contract\Maskable;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Mobile Subscriber Identity: not more than 15 digits, the Mobile Country Code (3), the Mobile Network Code (2
 * or 3, depending on the MCC) and the subscriber number. The MNC length is not in the value, so the caller passes it
 * to {@see mnc()} and {@see msin()}; the MCC table is under ITU copyright and is not embedded. At least six digits are
 * required (MCC, a two-digit MNC and one digit of subscriber number).
 *
 * @see https://www.etsi.org/deliver/etsi_ts/123000_123099/123003/17.07.00_60/ts_123003v170700p.pdf 3GPP TS 23.003 V17.7.0, 2.2
 * @see docs/sources/IDENTIFIERS.md "Imei, Imeisv, Imsi"
 */
final class Imsi extends AbstractStringValueObject implements Maskable
{
    private const string TYPE = 'IMSI';

    public function mcc(): string
    {
        return \substr($this->value, 0, 3);
    }

    /**
     * @param 2|3 $length
     *
     * @throws InvalidArgumentException when the length is not 2 or 3 or leaves no subscriber number
     */
    public function mnc(int $length): string
    {
        $this->assertMncLength($length);

        return \substr($this->value, 3, $length);
    }

    /**
     * @param 2|3 $mncLength
     *
     * @throws InvalidArgumentException when the MNC length is not 2 or 3 or leaves no subscriber number
     */
    public function msin(int $mncLength): string
    {
        $this->assertMncLength($mncLength);

        return \substr($this->value, 3 + $mncLength);
    }

    #[Override]
    public function masked(): string
    {
        return TelecomRules::mask($this->value, 5, 2);
    }

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('/^[0-9]{6,15}$/D', $value) !== 1) {
            throw TelecomRules::invalidFormat(self::TYPE);
        }

        return $value;
    }

    private function assertMncLength(int $length): void
    {
        if ($length !== 2 && $length !== 3) {
            throw new InvalidArgumentException('The MNC length is 2 or 3.');
        }
        if (\strlen($this->value) <= 3 + $length) {
            throw new InvalidArgumentException('The MNC length leaves no subscriber number.');
        }
    }
}
