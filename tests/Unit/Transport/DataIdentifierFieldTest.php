<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\DataIdentifierField;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Wmi;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DataIdentifierField::class)]
#[UsesClass(TransportRules::class)]
#[UsesClass(Wmi::class)]
final class DataIdentifierFieldTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function valid(): iterable
    {
        yield 'bare letter' => ['S000123', 'S', '000123'];
        yield 'one digit' => ['1PABC-1', '1P', 'ABC-1'];
        yield 'two digits' => ['25SLN12345', '25S', 'LN12345'];
        yield 'three digits' => ['123Xdata', '123X', 'data'];
        yield 'empty data' => ['Q', 'Q', ''];
        yield 'letters in data' => ['PAB12', 'P', 'AB12'];
        yield 'utf-8 data' => ['PŁódź', 'P', 'Łódź'];
        yield 'newline in data' => ["P a\nb", 'P', " a\nb"];
    }

    #[DataProvider('valid')]
    public function testParses(string $text, string $identifier, string $value): void
    {
        $field = DataIdentifierField::from($text);

        self::assertSame($identifier, $field->identifier());
        self::assertSame($value, $field->value());
        self::assertSame($text, (string) $field);
        self::assertSame($text, $field->jsonSerialize());
    }

    public function testEquality(): void
    {
        $field = new DataIdentifierField('1P', 'ABC');

        self::assertTrue($field->equals(DataIdentifierField::from('1PABC')));
        self::assertFalse($field->equals(DataIdentifierField::from('1PABD')));
        self::assertFalse($field->equals(DataIdentifierField::from('PABC')));
        self::assertFalse($field->equals(new Wmi('1G4')));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidText(): iterable
    {
        yield 'empty' => [''];
        yield 'digits only' => ['1234'];
        yield 'lowercase identifier' => ['1pABC'];
        yield 'four digits' => ['1234PABC'];
        yield 'group separator' => ["P\x1D"];
        yield 'record separator' => ["P\x1E"];
        yield 'end of transmission' => ["P\x04"];
        yield 'invalid utf-8' => ["P\xFF"];
    }

    #[DataProvider('invalidText')]
    public function testRefuses(string $text): void
    {
        try {
            DataIdentifierField::from($text);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('Data identifier field has an invalid format.', $e->getMessage());
        }
    }

    public function testConstructorRefusesABadIdentifier(): void
    {
        $this->expectException(InvalidTransportIdentifierException::class);
        new DataIdentifierField('1P ', 'ABC');
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalidParts(): iterable
    {
        yield 'leading space' => [' 1P', 'ABC'];
        yield 'trailing newline' => ["1P\n", 'ABC'];
        yield 'lowercase' => ['1p', 'ABC'];
        yield 'two letters' => ['PQ', 'ABC'];
        yield 'invalid utf-8 data' => ['P', "\xFF"];
        yield 'end of transmission in data' => ['P', "a\x04b"];
    }

    #[DataProvider('invalidParts')]
    public function testConstructorRefusesBadParts(string $identifier, string $value): void
    {
        $this->expectException(InvalidTransportIdentifierException::class);
        new DataIdentifierField($identifier, $value);
    }
}
