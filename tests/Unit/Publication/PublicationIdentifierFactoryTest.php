<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Ean13CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod11_2;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod37_36;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod97;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Mod11CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\RorCheckDigits;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Ark;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Doi;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isan;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isbn;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Ismn;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isni;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isrc;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Issn;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Iswc;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Orcid;
use MSSTC4PHP\ValueObject\Identifiers\Publication\PublicationIdentifierFactory;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Ror;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PublicationIdentifierFactory::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Ean13CheckDigit::class)]
#[UsesClass(Mod11CheckDigit::class)]
#[UsesClass(Iso7064Mod11_2::class)]
#[UsesClass(Iso7064Mod37_36::class)]
#[UsesClass(Iso7064Mod97::class)]
#[UsesClass(RorCheckDigits::class)]
#[UsesClass(Isbn::class)]
#[UsesClass(Issn::class)]
#[UsesClass(Ismn::class)]
#[UsesClass(Isrc::class)]
#[UsesClass(Iswc::class)]
#[UsesClass(Isan::class)]
#[UsesClass(Isni::class)]
#[UsesClass(Orcid::class)]
#[UsesClass(Doi::class)]
#[UsesClass(Ror::class)]
#[UsesClass(Ark::class)]
final class PublicationIdentifierFactoryTest extends TestCase
{
    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function unambiguous(): iterable
    {
        yield 'ISBN-10' => ['0-306-40615-2', Isbn::class];
        yield 'ISBN-13' => ['978-0-306-40615-7', Isbn::class];
        yield 'ISBN 979-10' => ['979-10-343-0062-4', Isbn::class];
        yield 'ISSN' => ['0317-8471', Issn::class];
        yield 'ISMN 979-0' => ['979-0-3452-4680-5', Ismn::class];
        yield 'ISMN old form' => ['M-3452-4680-5', Ismn::class];
        yield 'ISRC' => ['AA-6QZ-20-00047', Isrc::class];
        yield 'ISWC' => ['T-034.524.680-1', Iswc::class];
        yield 'ISAN' => ['B159-D8FA-0124-0000-K', Isan::class];
        yield 'ISAN with letters and no check characters' => ['B159-D8FA-0124-0000', Isan::class];
        yield 'ISNI labelled' => ['ISNI 1422 4586 3573 0476', Isni::class];
        yield 'ORCID URL' => ['https://orcid.org/0000-0002-1825-0097', Orcid::class];
        yield 'DOI' => ['10.1000/xyz-123', Doi::class];
        yield 'ROR' => ['https://ror.org/02mhbdp94', Ror::class];
        yield 'ARK' => ['ark:12025/654xz321', Ark::class];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('unambiguous')]
    public function testPicksTheOnlyMatch(string $value, string $class): void
    {
        self::assertInstanceOf($class, PublicationIdentifierFactory::fromString($value));
    }

    /**
     * @return iterable<string, array{string, list<class-string>}>
     */
    public static function ambiguous(): iterable
    {
        yield 'bare ISNI/ORCID value is also a check-less ISAN' => ['0000-0002-1825-0097', [Isan::class, Isni::class, Orcid::class]];
        yield 'compact ISNI/ORCID value' => ['1422458635730476', [Isan::class, Isni::class, Orcid::class]];
        yield 'ISNI/ORCID value with X is no ISAN' => ['0000-0002-1694-233X', [Isni::class, Orcid::class]];
    }

    /**
     * @param list<class-string> $candidates
     */
    #[DataProvider('ambiguous')]
    public function testOverlapsListEveryAcceptingType(string $value, array $candidates): void
    {
        try {
            PublicationIdentifierFactory::fromString($value);
            self::fail('Expected an exception.');
        } catch (AmbiguousValueException $e) {
            self::assertSame($candidates, $e->candidates());
            self::assertStringNotContainsString($value, $e->getMessage());
        }
    }

    #[DataProvider('notDois')]
    public function testPrefixlessNumbersAreNotADoi(string $value): void
    {
        try {
            PublicationIdentifierFactory::fromString($value);
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('Value is not a recognised publication identifier.', $e->getMessage());
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function notDois(): iterable
    {
        yield 'number pair' => ['1234/5678'];
        yield 'date' => ['2020/01/05'];
    }

    public function testNothingMatches(): void
    {
        try {
            PublicationIdentifierFactory::fromString('not an identifier!');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('Value is not a recognised publication identifier.', $e->getMessage());
        }
    }

    public function testTryFromStringBuildsWhatFromStringBuilds(): void
    {
        $value = '0317-8471';
        $built = PublicationIdentifierFactory::tryFromString($value);

        self::assertInstanceOf(Issn::class, $built);
        self::assertEquals(PublicationIdentifierFactory::fromString($value), $built);
    }

    public function testTryFromStringIsNullForNoMatch(): void
    {
        self::assertNull(PublicationIdentifierFactory::tryFromString('not an identifier!'));
    }

    public function testTryFromStringIsNullForAnAmbiguousValue(): void
    {
        self::assertNull(PublicationIdentifierFactory::tryFromString('1422458635730476'));
    }
}
