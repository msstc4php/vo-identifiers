<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Telecom;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Imei;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Imeisv;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Imei::class)]
#[UsesClass(Luhn::class)]
#[UsesClass(TelecomRules::class)]
#[UsesClass(Imeisv::class)]
final class ImeiTest extends TestCase
{
    public function testSourceExample(): void
    {
        // 3GPP TS 23.003 annex B.3.
        $imei = new Imei('260531793113837');

        self::assertSame('26053179', $imei->tac());
        self::assertSame('311383', $imei->serialNumber());
        self::assertSame('7', $imei->checkDigit());
        self::assertTrue($imei->hasCheckDigit());
        self::assertSame($imei, $imei->withCheckDigit());
        self::assertSame('8', new Imei('490154203237518')->checkDigit());
    }

    public function testFourteenDigitsAreValid(): void
    {
        $imei = new Imei('26053179311383');

        self::assertFalse($imei->hasCheckDigit());
        self::assertSame('7', $imei->checkDigit());
        self::assertSame('260531793113837', $imei->withCheckDigit()->value);
        self::assertSame('26053179311383', $imei->value);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function forms(): iterable
    {
        yield 'compact' => ['490154203237518', '490154203237518'];
        yield 'spaces' => ['49 015420 323751 8', '490154203237518'];
        yield 'hyphens' => ['49-015420-323751-8', '490154203237518'];
        yield 'fourteen with spaces' => ['49 015420 323751', '49015420323751'];
    }

    #[DataProvider('forms')]
    public function testAcceptsPrintedForms(string $input, string $stored): void
    {
        self::assertSame($stored, (new Imei($input))->value);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check digit' => ['490154203237519', 'IMEI has an invalid check digit.'];
        yield 'thirteen' => ['4901542032375', 'IMEI has an invalid format.'];
        yield 'sixteen' => ['4901542032375180', 'IMEI has an invalid format.'];
        yield 'letter' => ['49015420323751A', 'IMEI has an invalid format.'];
        yield 'newline' => ["490154203237518\n", 'IMEI has an invalid format.'];
        yield 'empty' => ['', 'IMEI has an invalid format.'];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value, string $message): void
    {
        try {
            new Imei($value);
            self::fail('Expected an exception.');
        } catch (InvalidTelecomIdentifierException $e) {
            self::assertSame($message, $e->getMessage());
        }
    }

    public function testMasks(): void
    {
        self::assertSame('49015420*******', new Imei('490154203237518')->masked());
        self::assertSame('49015420******', new Imei('49015420323751')->masked());
    }

    public function testBothFormsOfOneImeiAreEqual(): void
    {
        $short = new Imei('26053179311383');
        $long = new Imei('260531793113837');

        self::assertTrue($short->equals($long));
        self::assertTrue($long->equals($short));
        self::assertTrue($long->equals(new Imei('26 053179 311383 7')));
        self::assertFalse($long->equals(new Imei('26053179311384')));
        self::assertFalse($long->equals(new Imeisv('2605317931138301')));
    }
}
