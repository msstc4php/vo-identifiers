<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use InvalidArgumentException;
use LogicException;
use MSSTC4PHP\ValueObject\Exception\InvalidValueObjectException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\DataIdentifierField;
use MSSTC4PHP\ValueObject\Identifiers\Transport\DataIdentifierString;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DataIdentifierString::class)]
#[UsesClass(DataIdentifierField::class)]
#[UsesClass(TransportRules::class)]
final class DataIdentifierStringTest extends TestCase
{
    private const string ENVELOPE = "[)>\x1E06\x1D25SLN12345\x1DS000001\x1D1PABC-1\x1E\x04";

    public function testParsesAndRoundTripsByteExact(): void
    {
        $message = new DataIdentifierString(self::ENVELOPE);

        self::assertSame(self::ENVELOPE, $message->value);
        self::assertSame(self::ENVELOPE, (string) $message);
        self::assertSame(self::ENVELOPE, $message->jsonSerialize());
        self::assertSame(['25SLN12345', 'S000001', '1PABC-1'], \array_map(strval(...), $message->fields()));
        self::assertSame('25S', $message->fields()[0]->identifier());
        self::assertSame('LN12345', $message->fields()[0]->value());
    }

    public function testTypedSerialAccessor(): void
    {
        $message = new DataIdentifierString(self::ENVELOPE);

        self::assertSame('000001', $message->serial());
        self::assertSame('ABC-1', $message->field('1P')?->value());
        self::assertNull($message->field('Q'));
    }

    public function testFieldsAreParsedOnce(): void
    {
        $message = new DataIdentifierString(self::ENVELOPE);

        self::assertSame($message->fields(), $message->fields());
        self::assertSame($message->fields()[1], $message->field('S'));
    }

    public function testSerialIsNullWithoutTheField(): void
    {
        self::assertNull(DataIdentifierString::fromFields([new DataIdentifierField('P', 'X')])->serial());
    }

    public function testTheFirstFieldWithAnIdentifierWins(): void
    {
        $message = DataIdentifierString::fromFields([new DataIdentifierField('S', 'A'), new DataIdentifierField('S', 'B')]);

        self::assertSame('A', $message->serial());
    }

    public function testBuildsBackFromFields(): void
    {
        $message = DataIdentifierString::fromFields([
            new DataIdentifierField('25S', 'LN12345'),
            new DataIdentifierField('S', '000001'),
            new DataIdentifierField('1P', 'ABC-1'),
        ]);

        self::assertSame(self::ENVELOPE, $message->value);
    }

    public function testBuildingFromNoFieldsIsACallerError(): void
    {
        try {
            DataIdentifierString::fromFields([]);
            self::fail('Expected an exception.');
        } catch (InvalidArgumentException $e) {
            self::assertNotInstanceOf(InvalidTransportIdentifierException::class, $e);
            self::assertSame('A data identifier string needs at least one field.', $e->getMessage());
        }
    }

    public function testUtf8DataSurvives(): void
    {
        $envelope = "[)>\x1E06\x1D1PŁódź\x1E\x04";

        self::assertSame($envelope, (new DataIdentifierString($envelope))->value);
    }

    public function testASerializedMessageHoldsOnlyItsValue(): void
    {
        $message = new DataIdentifierString(self::ENVELOPE);
        $serialized = \serialize($message);

        self::assertSame(\sprintf('O:%d:"%s":1:{s:5:"value";s:%d:"%s";}', \strlen(DataIdentifierString::class), DataIdentifierString::class, \strlen(self::ENVELOPE), self::ENVELOPE), $serialized);

        $restored = \unserialize($serialized);
        self::assertInstanceOf(DataIdentifierString::class, $restored);
        self::assertTrue($message->equals($restored));
        self::assertSame('000001', $restored->serial());
        self::assertSame(['25SLN12345', 'S000001', '1PABC-1'], \array_map(strval(...), $restored->fields()));
    }

    public function testForgedDerivedStateIsIgnored(): void
    {
        $class = DataIdentifierString::class;
        $forged = \sprintf(
            'O:%d:"%s":2:{s:5:"value";s:%d:"%s";s:%d:"%s";a:1:{i:0;O:%d:"%s":1:{s:5:"value";s:4:"Sbad";}}}',
            \strlen($class),
            $class,
            \strlen(self::ENVELOPE),
            self::ENVELOPE,
            \strlen("\0{$class}\0parsedFields"),
            "\0{$class}\0parsedFields",
            \strlen(DataIdentifierField::class),
            DataIdentifierField::class,
        );

        $restored = \unserialize($forged);

        self::assertInstanceOf(DataIdentifierString::class, $restored);
        self::assertSame(['25SLN12345', 'S000001', '1PABC-1'], \array_map(strval(...), $restored->fields()));
        self::assertSame('000001', $restored->serial());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function malformedPayloads(): iterable
    {
        $prefix = \sprintf('O:%d:"%s":', \strlen(DataIdentifierString::class), DataIdentifierString::class);

        yield 'invalid value' => [$prefix . '1:{s:5:"value";s:5:"hello";}'];
        yield 'no value' => [$prefix . '0:{}'];
        yield 'a value that is no string' => [$prefix . '1:{s:5:"value";i:1;}'];
    }

    #[DataProvider('malformedPayloads')]
    public function testMalformedPayloadsAreRefused(string $serialized): void
    {
        $this->expectException(InvalidValueObjectException::class);

        \unserialize($serialized);
    }

    public function testABuiltObjectRefusesUnserialize(): void
    {
        $message = new DataIdentifierString(self::ENVELOPE);

        try {
            $message->__unserialize(['value' => "[)>\x1E06\x1D1PA\x1E\x04"]);
            self::fail('Expected an exception.');
        } catch (LogicException $e) {
            self::assertSame('An identifier is immutable; __unserialize() runs only on a new object.', $e->getMessage());
        }

        self::assertSame(self::ENVELOPE, $message->value);
        self::assertSame('000001', $message->serial());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'empty' => [''];
        yield 'no envelope' => ['25SLN12345'];
        yield 'wrong compliance indicator' => ["[)<\x1E06\x1D1PA\x1E\x04"];
        yield 'format 05' => ["[)>\x1E05\x1D1PA\x1E\x04"];
        yield 'no format header separator' => ["[)>06\x1D1PA\x1E\x04"];
        yield 'no field' => ["[)>\x1E06\x1E\x04"];
        yield 'no leading group separator' => ["[)>\x1E061PA\x1E\x04"];
        yield 'no trailer' => ["[)>\x1E06\x1D1PA"];
        yield 'no end of transmission' => ["[)>\x1E06\x1D1PA\x1E"];
        yield 'text after the trailer' => ["[)>\x1E06\x1D1PA\x1E\x04\n"];
        yield 'empty field' => ["[)>\x1E06\x1D\x1D1PA\x1E\x04"];
        yield 'empty last field' => ["[)>\x1E06\x1D1PA\x1D\x1E\x04"];
        yield 'field without identifier' => ["[)>\x1E06\x1D123\x1E\x04"];
        yield 'lowercase identifier' => ["[)>\x1E06\x1D1pA\x1E\x04"];
        yield 'record separator inside' => ["[)>\x1E06\x1D1PA\x1E1PB\x1E\x04"];
        yield 'invalid utf-8' => ["[)>\x1E06\x1D1P\xFF\x1E\x04"];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new DataIdentifierString($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('Data identifier string has an invalid format.', $e->getMessage());
        }
    }
}
