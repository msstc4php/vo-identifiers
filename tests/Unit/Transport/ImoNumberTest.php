<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\ImoNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ImoNumber::class)]
#[UsesClass(TransportRules::class)]
final class ImoNumberTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function valid(): iterable
    {
        yield 'printed' => ['IMO 8712345'];
        yield 'compact' => ['IMO8712345'];
        yield 'lowercase' => ['imo 8712345'];
        yield 'digits only' => ['8712345'];
    }

    #[DataProvider('valid')]
    public function testStoresThePrefixedForm(string $input): void
    {
        $imo = new ImoNumber($input);

        self::assertSame('IMO8712345', $imo->value);
        self::assertSame('8712345', $imo->number());
        self::assertSame('IMO 8712345', $imo->formatted());
    }

    public function testCheckDigitIsNotVerified(): void
    {
        self::assertSame('IMO9074729', new ImoNumber('IMO 9074729')->value);
        self::assertSame('IMO9074720', new ImoNumber('IMO 9074720')->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'six digits' => ['IMO 871234'];
        yield 'eight digits' => ['IMO 87123456'];
        yield 'letters' => ['IMO 87123A5'];
        yield 'wrong prefix' => ['IMX 8712345'];
        yield 'twice' => ['IMOIMO8712345'];
        yield 'newline' => ["IMO8712345\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new ImoNumber($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('IMO number has an invalid format.', $e->getMessage());
        }
    }
}
