<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Telecom;

use MSSTC4PHP\ValueObject\Contract\Maskable;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Integrated Circuit Card Identifier (ITU-T E.118): the major industry identifier `89`, then the country code, the issuer
 * and the account, and a Luhn check digit (ISO/IEC 7812-1 annex B). The card stores at most 20 digits (ETSI TS 102 221); neither source gives a
 * minimum, so the shortest accepted number is derived from the structure: `89`, a country code, an issuer and an
 * account of at least one digit each, and the check digit, i.e. 6 digits. The issuer identification number (with the `89`
 * and the country code) has at most 7 digits, but where it ends is not in the value, so {@see issuerIdentifier()} gives
 * the raw first 7 digits.
 *
 * @see https://www.itu.int/dms_pub/itu-t/md/25/tsb/cir/T25-TSB-CIR-0136!!MSW-E.docx ITU-T TSB Circular 136 (IIN, MII 89, Luhn)
 * @see https://www.etsi.org/deliver/etsi_ts/102200_102299/102221/17.00.00_60/ts_102221v170000p.pdf ETSI TS 102 221 V17.0.0, 13.2
 * @see docs/sources/IDENTIFIERS.md "Iccid"
 */
final class Iccid extends AbstractStringValueObject implements Maskable
{
    private const string TYPE = 'ICCID';

    /**
     * The first 7 digits: `89`, the country code and the issuer, as far as the 7-digit maximum of the IIN reaches.
     */
    public function issuerIdentifier(): string
    {
        return \substr($this->value, 0, 7);
    }

    #[Override]
    public function masked(): string
    {
        return TelecomRules::mask($this->value, 4, 2);
    }

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('/^89[0-9]{4,18}$/D', $value) !== 1) {
            throw TelecomRules::invalidFormat(self::TYPE);
        }
        if (!Luhn::isValid($value)) {
            throw TelecomRules::invalidCheckDigit(self::TYPE);
        }

        return $value;
    }
}
