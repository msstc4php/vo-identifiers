<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;

/**
 * Serializes an identifier as its value alone and runs `parse()` again on unserialize, so state derived from the value is
 * never restored apart from the value it was validated with. Any other key of the payload is ignored.
 *
 * @internal
 */
trait ReparsesOnUnserialize
{
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
     * @throws InvalidTransportIdentifierException when the data holds no string value or the value is not valid
     * @throws LogicException when called on an object that already holds a value
     */
    public function __unserialize(array $data): void
    {
        if (isset($this->value)) {
            throw new LogicException('An identifier is immutable; __unserialize() runs only on a new object.');
        }

        $value = $data['value'] ?? null;
        if (!\is_string($value)) {
            throw new InvalidTransportIdentifierException('A serialized identifier must hold its value, as a string.');
        }

        $this->value = $this->parse($value);
    }
}
