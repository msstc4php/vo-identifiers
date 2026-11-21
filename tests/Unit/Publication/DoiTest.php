<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Mod11CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Doi;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Issn;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Doi::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Issn::class)]
#[UsesClass(Mod11CheckDigit::class)]
final class DoiTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'handbook 1' => ['10.1000/xyz-123', '10.1000/xyz-123'];
        yield 'handbook 2' => ['10.1000/123456', '10.1000/123456'];
        yield 'handbook 3' => ['10.1038/issn.1476-4687', '10.1038/issn.1476-4687'];
        yield 'doi: prefix' => ['doi:10.1000/xyz-123', '10.1000/xyz-123'];
        yield 'DOI: prefix' => ['DOI:10.1000/xyz-123', '10.1000/xyz-123'];
        yield 'https resolver' => ['https://doi.org/10.1000/xyz-123', '10.1000/xyz-123'];
        yield 'http dx resolver' => ['http://dx.doi.org/10.1000/xyz-123', '10.1000/xyz-123'];
        yield 'subdivided registrant' => ['10.1000.10/abc', '10.1000.10/abc'];
        yield 'case kept as given' => ['10.1000/AbC', '10.1000/AbC'];
        yield 'brackets and colons' => ['10.1002/(SICI)1097-4571(199806)49:8<693::AID-ASI4>3.0.CO;2-O', '10.1002/(SICI)1097-4571(199806)49:8<693::AID-ASI4>3.0.CO;2-O'];
        yield 'second slash' => ['10.1000/a/b/c', '10.1000/a/b/c'];
        yield 'unicode suffix' => ['10.1000/日本語/ключ-é', '10.1000/日本語/ключ-é'];
        yield 'inner space' => ['10.1000/a b', '10.1000/a b'];
        yield 'www resolver' => ['https://www.doi.org/10.1000/xyz-123', '10.1000/xyz-123'];
        yield 'www dx resolver' => ['https://www.dx.doi.org/10.1000/xyz-123', '10.1000/xyz-123'];
        yield 'percent-encoded resolver URL is decoded' => ['https://doi.org/10.1002/%28SICI%291097-4571%28199806%2949:8%3C693::AID-ASI4%3E3.0.CO;2-O', '10.1002/(SICI)1097-4571(199806)49:8<693::AID-ASI4>3.0.CO;2-O'];
        yield 'percent-encoded Unicode in a resolver URL' => ['https://doi.org/10.1000/%E6%97%A5%E6%9C%AC', '10.1000/日本'];
        yield 'percent sign kept in the bare form' => ['10.1000/a%28b', '10.1000/a%28b'];
        yield 'percent sign kept after doi:' => ['doi:10.1000/a%28b', '10.1000/a%28b'];
        yield 'other directory indicator' => ['11.1000/abc', '11.1000/abc'];
        yield 'other directory indicator without registrant' => ['11/abc', '11/abc'];
        yield 'three-digit directory indicator' => ['100/abc', '100/abc'];
        yield 'three-digit directory indicator with registrant' => ['100.5/abc', '100.5/abc'];
        yield 'single character suffix' => ['10.1000/a', '10.1000/a'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheBareForm(string $input, string $expected): void
    {
        self::assertSame($expected, (new Doi($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'no slash' => ['10.1000'];
        yield 'empty suffix' => ['10.1000/'];
        yield 'no registrant' => ['10./abc'];
        yield 'directory 10 without registrant' => ['10/abc'];
        yield 'letters in the directory' => ['a1.1000/abc'];
        yield 'empty directory' => ['.1000/abc'];
        yield 'percent-encoded invalid UTF-8 in a resolver URL' => ['https://doi.org/10.1000/a%FFb'];
        yield 'percent-encoded control character in a resolver URL' => ['https://doi.org/10.1000/a%01b'];
        yield 'percent-encoded newline at the end' => ['https://doi.org/10.1000/abc%0A'];
        yield 'letters in registrant' => ['10.abc/xyz'];
        yield 'empty registrant part' => ['10.1000./xyz'];
        yield 'leading space' => [' 10.1000/abc'];
        yield 'trailing space' => ['10.1000/abc '];
        yield 'leading space in suffix' => ['10.1000/ abc'];
        yield 'trailing newline' => ["10.1000/abc\n"];
        yield 'control character' => ["10.1000/a\x01b"];
        yield 'invalid UTF-8' => ["10.1000/a\xFFb"];
        yield 'other host' => ['https://example.org/10.1000/abc'];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Doi($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Doi('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('DOI has an invalid format.', $e->getMessage());
        }
    }

    public function testEqualityIgnoresAsciiCase(): void
    {
        $doi = new Doi('10.1000/AbC');

        self::assertTrue($doi->equals(new Doi('10.1000/abc')));
        self::assertTrue($doi->equals(new Doi('doi:10.1000/ABC')));
        self::assertFalse($doi->equals(new Doi('10.1000/abd')));
    }

    public function testEqualityKeepsNonAsciiCase(): void
    {
        self::assertFalse(new Doi('10.1000/É')->equals(new Doi('10.1000/é')));
    }

    public function testEqualityAgainstAnotherType(): void
    {
        self::assertFalse(new Doi('10.1000/abc')->equals(new Issn('0317-8471')));
    }

    public function testUnicodeSuffixRoundTrips(): void
    {
        $doi = new Doi('10.1000/日本語/ключ-é');

        self::assertSame('10.1000/日本語/ключ-é', (string) $doi);
        self::assertSame('"10.1000\/日本語\/ключ-é"', \json_encode($doi, JSON_UNESCAPED_UNICODE));
        self::assertTrue(Doi::from((string) $doi)->equals($doi));
    }

    public function testParts(): void
    {
        $doi = new Doi('10.1000/a/b');

        self::assertSame('10.1000', $doi->prefix());
        self::assertSame('a/b', $doi->suffix());
    }
}
