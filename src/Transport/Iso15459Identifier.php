<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\IssuingAgencyRegister;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * A unique identifier under ISO/IEC 15459: the issuing agency code, the company identification number and a serial
 * number, written together as at least 3 capital letters, digits, dashes and forward slashes, starting with a letter or
 * digit (the DoD UID Guide keeps `-` and `/` of a serial number in the UII under `25S`). As carried in an MH10.8.2
 * message it travels under data identifier `25S` or `J` (the license plate of a transport unit, at most 35 capital
 * letters and digits), see {@see fromField()}.
 * The agency code is read from the start by the AIM register (version 2026-06-16); a value whose start is in no entry is
 * accepted and decodes to null. Only the code and agency name of the register are embedded.
 *
 * @see https://www.aimglobal.org/registration-authority-iso-iec-15459/ AIM, Registration Authority for ISO/IEC 15459
 * @see https://www.aimglobal.org/wp-content/uploads/2026/06/Register-IAC-15459-2-20260616.pdf Register of Issuing Agency Codes
 * @see https://www.mhi.org/standards/mh10 ANSI MH10.8.2, data identifiers 25S and J
 * @see https://webarchive.library.unt.edu/web/20130223193613id_/http://www.acq.osd.mil/dpap/UID/attachments/DoDUIDGuideVer2_5.pdf DoD Guide to Uniquely Identifying Items v2.5, Business Rule 21 and footnote 88
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "Iso15459Identifier and DataIdentifierString"
 */
final class Iso15459Identifier extends AbstractStringValueObject
{
    private const string TYPE = 'ISO/IEC 15459 identifier';

    private const int LICENSE_PLATE_MAX_LENGTH = 35;

    private const int DOD_UII_MAX_LENGTH = 50;

    /**
     * The issuing agency code at the start of the identifier; null when no register entry starts it.
     *
     * @return non-empty-string|null
     */
    public function issuingAgencyCode(): ?string
    {
        return IssuingAgencyRegister::codeOf($this->value);
    }

    /**
     * @return non-empty-string|null
     */
    public function issuingAgencyName(): ?string
    {
        $code = $this->issuingAgencyCode();

        return $code === null ? null : IssuingAgencyRegister::nameOf($code);
    }

    /** Business Rule 19 of the DoD UID Guide v2.5 caps a UII at 50 characters; the value is never refused for more. */
    public function hasValidDodUiiLength(): bool
    {
        return \strlen($this->value) <= self::DOD_UII_MAX_LENGTH;
    }

    /**
     * @throws InvalidTransportIdentifierException when the field is not under data identifier `25S` or `J`, its data is no identifier, or a `J` license plate is longer than 35 characters or holds a dash or slash
     */
    public static function fromField(DataIdentifierField $field): self
    {
        if ($field->identifier() !== '25S' && $field->identifier() !== 'J') {
            throw TransportRules::fail(self::TYPE, Reason::WRONG_DATA_IDENTIFIER);
        }

        if ($field->identifier() === 'J' && (\strlen($field->data()) > self::LICENSE_PLATE_MAX_LENGTH || \preg_match('/[^A-Z0-9]/i', $field->data()) === 1)) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return new self($field->data());
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('~^[A-Z0-9][A-Z0-9/-]{2,}$~D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
