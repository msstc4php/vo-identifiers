<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Maritime Mobile Service Identity (ITU-R M.585): nine contiguous digits. {@see type()} classifies by the leading digits and
 * {@see mid()} gives the three maritime identification digits; the MID table is under ITU copyright and is not embedded,
 * so the digits are not decoded to a country. A well-formed number the recommendation does not classify is
 * {@see MmsiStationType::Unclassified}, as is a form whose MID would not start with 2 to 7.
 *
 * @see https://www.itu.int/dms_pubrec/itu-r/rec/m/R-REC-M.585-10-202604-I!!PDF-E.pdf ITU-R M.585-10, Annexes 1 and 2
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "Mmsi"
 */
final class Mmsi extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 9;

    private const string TYPE = 'MMSI';

    public function type(): MmsiStationType
    {
        $digits = $this->value;

        return match (true) {
            \str_starts_with($digits, '00') && $this->isMid($digits[2]) => MmsiStationType::CoastStation,
            $digits[0] === '0' && $this->isMid($digits[1]) => MmsiStationType::GroupShip,
            \str_starts_with($digits, '111') && $this->isMid($digits[3]) => MmsiStationType::SarAircraft,
            \str_starts_with($digits, '99') && $this->isMid($digits[2]) => MmsiStationType::AidToNavigation,
            \str_starts_with($digits, '98') && $this->isMid($digits[2]) => MmsiStationType::CraftAssociated,
            \in_array(\substr($digits, 0, 3), ['970', '972', '974'], true) => MmsiStationType::DistressDevice,
            $this->isMid($digits[0]) => MmsiStationType::Ship,
            default => MmsiStationType::Unclassified,
        };
    }

    /**
     * @return string|null null for the types whose digits carry no MID (AIS-SART, MOB, EPIRB-AIS, unclassified)
     */
    public function mid(): ?string
    {
        $offset = match ($this->type()) {
            MmsiStationType::Ship => 0,
            MmsiStationType::GroupShip => 1,
            MmsiStationType::CoastStation, MmsiStationType::AidToNavigation, MmsiStationType::CraftAssociated => 2,
            MmsiStationType::SarAircraft => 3,
            MmsiStationType::DistressDevice, MmsiStationType::Unclassified => null,
        };

        return $offset === null ? null : \substr($this->value, $offset, 3);
    }

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('/^[0-9]{9}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }

    /**
     * A MID is a number from 201 to 775 (the table of M.585), so its first digit is 2 to 7.
     */
    private function isMid(string $firstDigit): bool
    {
        return \str_contains('234567', $firstDigit);
    }
}
