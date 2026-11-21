<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\IssuingAgencyRegister;
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
#[UsesClass(IssuingAgencyRegister::class)]
final class Iso15459IdentifierTest extends TestCase
{
    public function testUppercases(): void
    {
        self::assertSame('LN12345678', (new Iso15459Identifier('ln12345678'))->value);
    }

    public function testShortestAndLongValues(): void
    {
        self::assertSame('LN1', (new Iso15459Identifier('LN1'))->value);
        self::assertSame('LN' . \str_repeat('1', 60), (new Iso15459Identifier('LN' . \str_repeat('1', 60)))->value);
    }

    public function testSerialNumberFieldHasNoLengthCap(): void
    {
        $value = 'LN' . \str_repeat('1', 60);

        self::assertSame($value, Iso15459Identifier::fromField(new DataIdentifierField('25S', $value))->value);
    }

    public function testLicensePlateIsCappedAt35Characters(): void
    {
        $plate = 'LN' . \str_repeat('1', 33);
        self::assertSame($plate, Iso15459Identifier::fromField(new DataIdentifierField('J', $plate))->value);

        try {
            Iso15459Identifier::fromField(new DataIdentifierField('J', $plate . '1'));
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('ISO/IEC 15459 identifier has an invalid format.', $e->getMessage());
        }
    }

    /**
     * @return iterable<string, array{string, string|null, string|null}>
     */
    public static function agencies(): iterable
    {
        yield 'two letters' => ['LN12345', 'LN', 'ABOL SOFTWARE INC.'];
        yield 'three letters' => ['VBS12345', 'VBS', 'Bosch und Siemens Hausgeräte GmbH'];
        yield 'one letter' => ['J12345678', 'J', 'Universal Postal Union'];
        yield 'one digit' => ['0614141', '0', 'GS1 Global Office'];
        yield 'last digit' => ['9614141', '9', 'GS1 Global Office'];
        yield 'DOI Foundation' => ['XID10001', 'XID', 'DOI Foundation'];
        yield 'D alone' => ['D12345', 'D', 'NATO Support and Procurement Agency (NSPA)'];
        yield 'unknown prefix' => ['ZZ12345', null, null];
        yield 'near miss of a three-letter code' => ['KCB1234', null, null];
        yield 'lowercase is uppercased' => ['ln12345', 'LN', 'ABOL SOFTWARE INC.'];
    }

    #[DataProvider('agencies')]
    public function testDecodesTheIssuingAgency(string $value, ?string $code, ?string $name): void
    {
        $identifier = new Iso15459Identifier($value);

        self::assertSame($code, $identifier->issuingAgencyCode());
        self::assertSame($name, $identifier->issuingAgencyName());
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
