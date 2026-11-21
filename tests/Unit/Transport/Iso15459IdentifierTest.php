<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\DataIdentifierField;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Iso15459Identifier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Iso15459Identifier::class)]
#[UsesClass(DataIdentifierField::class)]
#[UsesClass(TransportRules::class)]
final class Iso15459IdentifierTest extends TestCase
{
    public function testUppercases(): void
    {
        self::assertSame('LN12345678', (new Iso15459Identifier('ln12345678'))->value);
    }

    public function testBoundaryLengths(): void
    {
        self::assertSame('LN1', (new Iso15459Identifier('LN1'))->value);
        self::assertSame(\str_repeat('A', 35), (new Iso15459Identifier(\str_repeat('A', 35)))->value);
    }

    public function testCarriedBySerialNumberDataIdentifier(): void
    {
        self::assertSame('LN12345', Iso15459Identifier::fromField(new DataIdentifierField('25S', 'LN12345'))->value);
    }

    public function testCarriedByLicensePlateDataIdentifier(): void
    {
        self::assertSame('LN12345', Iso15459Identifier::fromField(new DataIdentifierField('J', 'LN12345'))->value);
    }

    public function testAnotherDataIdentifierIsRefused(): void
    {
        try {
            Iso15459Identifier::fromField(new DataIdentifierField('S', 'LN12345'));
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('ISO/IEC 15459 identifier is not carried by data identifier 25S or J.', $e->getMessage());
        }
    }

    public function testBadDataInAField(): void
    {
        $this->expectException(InvalidTransportIdentifierException::class);
        Iso15459Identifier::fromField(new DataIdentifierField('J', 'LN-1'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'two' => ['LN'];
        yield 'thirty-six' => [\str_repeat('A', 36)];
        yield 'hyphen' => ['LN-123'];
        yield 'space' => ['LN 123'];
        yield 'newline' => ["LN123\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new Iso15459Identifier($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('ISO/IEC 15459 identifier has an invalid format.', $e->getMessage());
        }
    }
}
