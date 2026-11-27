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

#[CoversClass(Imeisv::class)]
#[UsesClass(Imei::class)]
#[UsesClass(Luhn::class)]
#[UsesClass(TelecomRules::class)]
final class ImeisvTest extends TestCase
{
    public function testParts(): void
    {
        $imeisv = new Imeisv('2605317931138301');

        self::assertSame('26053179', $imeisv->tac());
        self::assertSame('311383', $imeisv->serialNumber());
        self::assertSame('01', $imeisv->softwareVersion());
        self::assertSame('26053179311383', $imeisv->toImei()->value);
        self::assertSame('260531793113837', $imeisv->toImei()->withCheckDigit()->value);
    }

    public function testAcceptsSpacesAndHyphens(): void
    {
        self::assertSame('2605317931138301', new Imeisv('26-053179-311383-01')->value);
        self::assertSame('2605317931138301', new Imeisv('26 053179 311383 01')->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'fifteen' => ['260531793113837'];
        yield 'seventeen' => ['26053179311383011'];
        yield 'letter' => ['260531793113830A'];
        yield 'newline' => ["2605317931138301\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new Imeisv($value);
            self::fail('Expected an exception.');
        } catch (InvalidTelecomIdentifierException $e) {
            self::assertSame('IMEISV has an invalid format.', $e->getMessage());
        }
    }

    public function testMasks(): void
    {
        self::assertSame('26053179******01', new Imeisv('2605317931138301')->masked());
    }
}
