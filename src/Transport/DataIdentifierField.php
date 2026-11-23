<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use LogicException;
use MSSTC4PHP\ValueObject\Contract\StringConvertible;
use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Exception\InvalidValueObjectException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use Override;

/**
 * One data element of an ANSI MH10.8.2 message: a data identifier (capital letter preceded by up to three digits, `25S`,
 * `1P`, `S`) followed by its data, written together (`1PABC123`). The data is any UTF-8 text without the separators
 * RS, GS and EOT. The identifier is not looked up: the MH10.8.2 table is copyrighted and not embedded. It serializes as
 * its string form and is validated again on unserialize.
 *
 * @see https://www.mhi.org/standards/mh10 ANSI MH10.8.2, clause 4 (data identifier format)
 * @see docs/sources/IDENTIFIERS.md "Iso15459Identifier and DataIdentifierString"
 */
final readonly class DataIdentifierField implements StringConvertible, ValueObject
{
    private const string TYPE = 'Data identifier field';

    /** The data identifier and the data written together, as {@see __toString()} gives them. */
    public string $value;

    private string $dataIdentifier;

    private string $data;

    /**
     * @throws InvalidTransportIdentifierException when the identifier is not a data identifier or the data holds RS,
     *                                             GS, EOT or invalid UTF-8
     */
    public function __construct(string $identifier, string $data)
    {
        if (\preg_match('/^[0-9]{0,3}[A-Z]$/D', $identifier) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        if (\preg_match('//u', $data) !== 1 || \strpbrk($data, "\x04\x1D\x1E") !== false) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        $this->dataIdentifier = $identifier;
        $this->data = $data;
        $this->value = $identifier . $data;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return array{value: string}
     */
    public function __serialize(): array
    {
        return ['value' => $this->value];
    }

    /**
     * @param array<array-key, mixed> $data untrusted: whatever the serialized string holds
     *
     * @throws InvalidValueObjectException when the data holds no string value, or the transport exception when the
     *                                     value is not valid
     * @throws LogicException when called on an object that already holds a value
     */
    public function __unserialize(array $data): void
    {
        if (isset($this->value)) {
            throw new LogicException('A value object is immutable; __unserialize() runs only on a new object.');
        }

        $value = $data['value'] ?? null;
        if (!\is_string($value)) {
            throw new InvalidValueObjectException(\sprintf('A serialized %s must hold its value as a string.', self::class));
        }

        $field = self::from($value);
        $this->dataIdentifier = $field->dataIdentifier;
        $this->data = $field->data;
        $this->value = $field->value;
    }

    /**
     * @throws InvalidTransportIdentifierException when the value does not start with a data identifier or its data is
     *                                             not allowed
     */
    #[Override]
    public static function from(string $value): static
    {
        if (\preg_match('/^[0-9]{0,3}[A-Z]/', $value, $match) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return new self($match[0], \substr($value, \strlen($match[0])));
    }

    public static function tryFrom(string $value): ?self
    {
        try {
            return self::from($value);
        } catch (InvalidTransportIdentifierException) {
            return null;
        }
    }

    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) instanceof self;
    }

    /**
     * The data identifier (`25S`, `1P`, `S`).
     */
    public function identifier(): string
    {
        return $this->dataIdentifier;
    }

    /**
     * The data after the data identifier; may be empty.
     */
    public function data(): string
    {
        return $this->data;
    }

    #[Override]
    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $other->value === $this->value;
    }

    #[Override]
    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
