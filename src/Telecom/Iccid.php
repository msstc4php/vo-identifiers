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
 * and the account, and a Luhn check digit (ISO/IEC 7812-1 annex B). E.118 (09/2025) prescribes 19 digits for new numbers
 * ({@see hasE118Length()}) and leaves previous assignments unaffected; the card stores at most 20 digits (ETSI TS 102 221).
 * No source gives a minimum, so the shortest accepted number is derived from the structure: `89`, a country code, an issuer and an
 * account of at least one digit each, and the check digit, i.e. 6 digits. The issuer identification number (with the `89`
 * and the country code) has at most 7 digits, but where it ends is not in the value, so {@see issuerIdentifier()} gives
 * the raw first 7 digits.
 *
 * @see https://www.itu.int/rec/T-REC-E.118-202509-I/en ITU-T E.118 (09/2025), clauses 1 and 6
 * @see https://www.itu.int/dms_pub/itu-t/md/25/tsb/cir/T25-TSB-CIR-0136!!MSW-E.docx ITU-T TSB Circular 136 (IIN, MII 89, Luhn)
 * @see https://www.etsi.org/deliver/etsi_ts/102200_102299/102221/17.00.00_60/ts_102221v170000p.pdf ETSI TS 102 221 V17.0.0, 13.2
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "Iccid"
 */
final class Iccid extends AbstractStringValueObject implements Maskable
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 20;

    private const string TYPE = 'ICCID';

    /**
     * The first 7 digits: `89`, the country code and the issuer, as far as the 7-digit maximum of the IIN reaches.
     */
    public function issuerIdentifier(): string
    {
        return \substr($this->value, 0, 7);
    }

    /**
     * Report-only: whether the number has the 19 digits ITU-T E.118 (09/2025) prescribes. Other lengths are still
     * accepted: E.118 leaves previous assignments unaffected and ETSI TS 102 221 stores up to 20 digits.
     */
    public function hasE118Length(): bool
    {
        return \strlen($this->value) === 19;
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
