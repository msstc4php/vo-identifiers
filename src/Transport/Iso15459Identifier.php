<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * A unique identifier under ISO/IEC 15459: the issuing agency code, the company identification number and a serial
 * number, written together as 3 to 35 capital letters and digits. As carried in an MH10.8.2 message it travels under data
 * identifier `25S` (or `J`, the license plate of a transport unit), see {@see fromField()}. Format only: the split
 * between the agency code and the company number would need the AIM register, which is not embedded.
 *
 * @see https://www.aimglobal.org/registration-authority-iso-iec-15459/ AIM, Registration Authority for ISO/IEC 15459
 * @see https://www.mhi.org/standards/mh10 ANSI MH10.8.2, data identifiers 25S and J
 * @see docs/sources/IDENTIFIERS.md "Iso15459Identifier and DataIdentifierString"
 */
final class Iso15459Identifier extends AbstractStringValueObject
{
    private const string TYPE = 'ISO/IEC 15459 identifier';

    /**
     * @throws InvalidTransportIdentifierException when the field is not under data identifier `25S` or `J`, or its data is no identifier
     */
    public static function fromField(DataIdentifierField $field): self
    {
        if ($field->identifier() !== '25S' && $field->identifier() !== 'J') {
            throw TransportRules::fail(self::TYPE, 'is not carried by data identifier 25S or J');
        }

        return new self($field->value());
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('/^[A-Z0-9]{3,35}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
