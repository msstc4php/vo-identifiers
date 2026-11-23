<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * An ISO/IEC 15434 message in format `06` (data using ANSI MH10.8.2 data identifiers):
 * `[)>` RS `06` GS field GS field ... RS EOT, with RS 0x1E, GS 0x1D and EOT 0x04. The value is the envelope exactly as
 * given, separators included, so a message round-trips byte for byte; {@see fields()} parses it into typed fields.
 *
 * @see https://www.iso.org/standard/36783.html ISO/IEC 15434:2005, 4.1 and Table 1
 * @see https://www.mhi.org/standards/mh10 ANSI MH10.8.2, clause 4
 * @see docs/sources/IDENTIFIERS.md "Iso15459Identifier and DataIdentifierString"
 */
final class DataIdentifierString extends AbstractStringValueObject
{
    private const string TYPE = 'Data identifier string';

    private const string HEADER = "[)>\x1E06";

    private const string TRAILER = "\x1E\x04";

    private const string FIELD_SEPARATOR = "\x1D";

    /** @var list<DataIdentifierField> */
    private array $parsedFields = [];

    /**
     * @param non-empty-list<DataIdentifierField> $fields
     *
     * @throws InvalidArgumentException when there is no field: a programming error, not an invalid identifier, so
     *                                  {@see tryFrom()} does not absorb it
     */
    public static function fromFields(array $fields): self
    {
        if ($fields === []) {
            throw new InvalidArgumentException('A data identifier string needs at least one field.');
        }

        $body = '';
        foreach ($fields as $field) {
            $body .= self::FIELD_SEPARATOR . $field;
        }

        return new self(self::HEADER . $body . self::TRAILER);
    }

    /**
     * Never empty: the envelope needs at least one field.
     *
     * @return list<DataIdentifierField>
     */
    public function fields(): array
    {
        return $this->parsedFields;
    }

    /**
     * The first field with the given identifier.
     */
    public function field(string $identifier): ?DataIdentifierField
    {
        foreach ($this->parsedFields as $field) {
            if ($field->identifier() === $identifier) {
                return $field;
            }
        }

        return null;
    }

    /**
     * The data of the serial number field (data identifier `S`).
     */
    public function serialNumber(): ?string
    {
        return $this->field('S')?->data();
    }

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('/^\[\)>\x1E06\x1D([^\x04\x1D\x1E]+(?:\x1D[^\x04\x1D\x1E]+)*)\x1E\x04$/D', $value, $match) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        try {
            $this->parsedFields = \array_map(DataIdentifierField::from(...), \explode(self::FIELD_SEPARATOR, $match[1]));
        } catch (InvalidTransportIdentifierException) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
