<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\AirWaybillCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\AirWaybillNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AirWaybillNumber::class)]
#[UsesClass(TransportRules::class)]
#[UsesClass(AirWaybillCheckDigit::class)]
final class AirWaybillNumberTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function valid(): iterable
    {
        yield 'printed' => ['020-12345675'];
        yield 'compact' => ['02012345675'];
        // IATA Resolution 600a, Attachment 'A' 5.2.1: "a space may be inserted in the serial number ... between the fourth and fifth digits".
        yield 'printed with the serial space' => ['020-1234 5675'];
        yield 'compact with the serial space' => ['0201234 5675'];
    }

    #[DataProvider('valid')]
    public function testAcceptsTheAbfExample(string $input): void
    {
        $awb = new AirWaybillNumber($input);

        self::assertSame('02012345675', $awb->value);
        self::assertSame('020', $awb->airlinePrefix());
        self::assertSame('1234567', $awb->serialNumber());
        self::assertSame('5', $awb->checkDigit());
        self::assertSame('020-12345675', $awb->formatted());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function iataExamples(): iterable
    {
        // IATA Resolution 600a, Attachment 'B' Appendix 'D': "The complete serial number shall read as '12345675'."
        yield 'Appendix D serial' => ['777-12345675', '77712345675'];
        // IATA Resolution 606 bar code examples in the same handbook: "air waybill 777-76543213", "air waybill 777-32176546".
        yield 'Resolution 606 example b' => ['777-76543213', '77776543213'];
        yield 'Resolution 606 example c' => ['777-32176546', '77732176546'];
    }

    #[DataProvider('iataExamples')]
    public function testAcceptsTheIataExamples(string $input, string $expected): void
    {
        self::assertSame($expected, new AirWaybillNumber($input)->value);
    }

    public function testAWrongCheckDigitIsRefused(): void
    {
        try {
            new AirWaybillNumber('020-12345676');
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('Air waybill number has a wrong check digit.', $e->getMessage());
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'spaces' => ['020 12345675'];
        yield 'hyphen before the check digit' => ['020-1234567-5'];
        yield 'two hyphens' => ['020--12345675'];
        yield 'space elsewhere in the serial' => ['020-123 45675'];
        yield 'two serial spaces' => ['020-1234  5675'];
        yield 'space and hyphen after the prefix' => ['020- 1234 5675'];
        yield 'hyphen in the serial' => ['020-1234-5675'];
        yield 'trailing space' => ['020-12345675 '];
        yield 'extra leading character' => ['X020-12345675'];
        yield 'ten digits' => ['020-1234567'];
        yield 'twelve digits' => ['020-123456755'];
        yield 'letter' => ['020-1234567A'];
        yield 'newline' => ["020-12345675\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new AirWaybillNumber($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('Air waybill number has an invalid format.', $e->getMessage());
        }
    }
}
