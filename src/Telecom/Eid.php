<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Telecom;

use MSSTC4PHP\ValueObject\Contract\Maskable;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod97;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * eUICC identifier: 32 digits, the last two being the ISO/IEC 7064 MOD 97-10 check digits. No prefix is enforced: SGP.29
 * forbids `89` for the EIDs it defines, while EIDs of the E.118 scheme start with it.
 *
 * @see https://www.gsma.com/solutions-and-impact/technologies/esim/wp-content/uploads/2024/03/SGP.29-v1.1.pdf GSMA SGP.29 v1.1, EID.R01 and clause 10
 * @see docs/sources/IDENTIFIERS.md "Eid"
 */
final class Eid extends AbstractStringValueObject implements Maskable
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 32;

    private const string TYPE = 'EID';

    #[Override]
    public function masked(): string
    {
        return TelecomRules::mask($this->value, 8, 2);
    }

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('/^[0-9]{32}$/D', $value) !== 1) {
            throw TelecomRules::invalidFormat(self::TYPE);
        }
        if (!Iso7064Mod97::isValid($value)) {
            throw TelecomRules::invalidCheckDigit(self::TYPE);
        }

        return $value;
    }
}
