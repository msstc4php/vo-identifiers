<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod97;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\RorCheckDigits;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Ror;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Ror::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(RorCheckDigits::class)]
#[UsesClass(Iso7064Mod97::class)]
final class RorTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield '02mhbdp94' => ['02mhbdp94', '02mhbdp94'];
        yield '01an7q238' => ['01an7q238', '01an7q238'];
        yield '03yrm5c26' => ['03yrm5c26', '03yrm5c26'];
        yield '05gq02987' => ['05gq02987', '05gq02987'];
        yield '0168r3w48' => ['0168r3w48', '0168r3w48'];
        yield 'https URL' => ['https://ror.org/02mhbdp94', '02mhbdp94'];
        yield 'http URL' => ['http://ror.org/02mhbdp94', '02mhbdp94'];
        yield 'host only' => ['ror.org/02mhbdp94', '02mhbdp94'];
        yield 'www' => ['https://www.ror.org/02mhbdp94', '02mhbdp94'];
        yield 'uppercase' => ['https://ROR.ORG/02MHBDP94', '02mhbdp94'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheBareLowercaseForm(string $input, string $expected): void
    {
        self::assertSame($expected, (new Ror($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check' => ['02mhbdp95'];
        yield 'does not start with 0' => ['12mhbdp94'];
        yield 'excluded letter I' => ['02mhbdi94'];
        yield 'excluded letter L' => ['02mhbdl94'];
        yield 'excluded letter O' => ['02mhbdo94'];
        yield 'excluded letter U' => ['02mhbdu94'];
        yield 'letters in the check' => ['02mhbdpab'];
        yield 'eight characters' => ['02mhbd94'];
        yield 'ten characters' => ['02mhbdp940'];
        yield 'other host' => ['https://example.org/02mhbdp94'];
        yield 'trailing path' => ['https://ror.org/02mhbdp94/x'];
        yield 'trailing newline' => ["02mhbdp94\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Ror($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Ror('02mhbdp95');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ROR has a wrong check digit.', $e->getMessage());
        }

        try {
            new Ror('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ROR has an invalid format.', $e->getMessage());
        }
    }

    public function testUri(): void
    {
        self::assertSame('https://ror.org/02mhbdp94', new Ror('02MHBDP94')->uri());
    }
}
