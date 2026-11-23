<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\IssuingAgencyRegister;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * A unique identifier under ISO/IEC 15459: the issuing agency code, the company identification number and a serial
 * number, written together as at least 3 capital letters and digits. As carried in an MH10.8.2 message it travels under
 * data identifier `25S` or `J` (the license plate of a transport unit, at most 35 characters), see {@see fromField()}.
 * The agency code is read from the start by the AIM register (version 2026-06-16); a value whose start is in no entry is
 * accepted and decodes to null. Only the code and agency name of the register are embedded.
 *
 * @see https://www.aimglobal.org/registration-authority-iso-iec-15459/ AIM, Registration Authority for ISO/IEC 15459
 * @see https://www.aimglobal.org/wp-content/uploads/2026/06/Register-IAC-15459-2-20260616.pdf Register of Issuing Agency Codes
 * @see https://www.mhi.org/standards/mh10 ANSI MH10.8.2, data identifiers 25S and J
 * @see docs/sources/IDENTIFIERS.md "Iso15459Identifier and DataIdentifierString"
 */
final class Iso15459Identifier extends AbstractStringValueObject
{
    private const string TYPE = 'ISO/IEC 15459 identifier';

    private const int LICENSE_PLATE_MAX_LENGTH = 35;

    /**
     * The issuing agency code at the start of the identifier; null when no register entry starts it.
     */
    public function issuingAgencyCode(): ?string
    {
        return IssuingAgencyRegister::codeOf($this->value);
    }

    public function issuingAgencyName(): ?string
    {
        $code = $this->issuingAgencyCode();

        return $code === null ? null : IssuingAgencyRegister::nameOf($code);
    }

    /**
     * @throws InvalidTransportIdentifierException when the field is not under data identifier `25S` or `J`, its data is no identifier, or a `J` license plate is longer than 35 characters
     */
    public static function fromField(DataIdentifierField $field): self
    {
        if ($field->identifier() !== '25S' && $field->identifier() !== 'J') {
            throw TransportRules::fail(self::TYPE, 'is not carried by data identifier 25S or J');
        }

        if ($field->identifier() === 'J' && \strlen($field->data()) > self::LICENSE_PLATE_MAX_LENGTH) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return new self($field->data());
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('/^[A-Z0-9]{3,}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
