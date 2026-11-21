<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Energy;

use MSSTC4PHP\ValueObject\Identifiers\Energy\EicObjectType;
use MSSTC4PHP\ValueObject\Identifiers\Energy\EnergyIdentificationCode;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidEnergyIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\EicCheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EnergyIdentificationCode::class)]
#[CoversClass(EicObjectType::class)]
#[UsesClass(CommodityRules::class)]
#[UsesClass(EicCheckDigit::class)]
final class EnergyIdentificationCodeTest extends TestCase
{
    public function testDecodesTheIgSampleCode(): void
    {
        // ENTSO-E EIC Data Exchange IG v1.2: `11XRWENET12345-` gets the check character `2`.
        $eic = new EnergyIdentificationCode('11XRWENET12345-2');

        self::assertSame('11', $eic->issuingOffice());
        self::assertSame('X', $eic->objectTypeCode());
        self::assertSame(EicObjectType::Party, $eic->objectType());
        self::assertSame('2', $eic->checkCharacter());
        self::assertSame('11XRWENET12345-2', (string) new EnergyIdentificationCode('11xrwenet12345-2'));
    }

    public function testAcceptsAnAreaCodeFromTheIg(): void
    {
        $eic = new EnergyIdentificationCode('11Y123456789012T');

        self::assertSame(EicObjectType::Area, $eic->objectType());
    }

    /**
     * @return iterable<string, array{string, EicObjectType}>
     */
    public static function letters(): iterable
    {
        foreach (EicObjectType::cases() as $case) {
            yield $case->name => [$case->value, $case];
        }
    }

    #[DataProvider('letters')]
    public function testObjectTypeLetters(string $letter, EicObjectType $expected): void
    {
        self::assertSame($expected, EicObjectType::from($letter));
    }

    public function testUnknownObjectTypeLetterIsStillAccepted(): void
    {
        $body = '11Q123456789012';
        $check = null;
        foreach (\str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ') as $candidate) {
            if (EicCheckDigit::isValid($body . $candidate)) {
                $check = $candidate;
            }
        }
        self::assertNotNull($check);

        $eic = new EnergyIdentificationCode($body . $check);

        self::assertNull($eic->objectType());
        self::assertSame('Q', $eic->objectTypeCode());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check' => ['11XRWENET12345-3', 'Energy Identification Code has an invalid check character.'];
        yield 'hyphen as check' => ['11XRWENET12345--', 'Energy Identification Code has an invalid format.'];
        yield 'short' => ['11XRWENET12345', 'Energy Identification Code has an invalid format.'];
        yield 'long' => ['11XRWENET12345-22', 'Energy Identification Code has an invalid format.'];
        yield 'space' => ['11XRWENET 2345-2', 'Energy Identification Code has an invalid format.'];
        yield 'newline' => ["11XRWENET12345-2\n", 'Energy Identification Code has an invalid format.'];
        yield 'empty' => ['', 'Energy Identification Code has an invalid format.'];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value, string $message): void
    {
        try {
            new EnergyIdentificationCode($value);
            self::fail('Expected an exception.');
        } catch (InvalidEnergyIdentifierException $e) {
            self::assertSame($message, $e->getMessage());
        }
    }
}
