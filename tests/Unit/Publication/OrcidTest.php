<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod11_2;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isni;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Orcid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Orcid::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Iso7064Mod11_2::class)]
#[UsesClass(Isni::class)]
final class OrcidTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'bare' => ['0000-0002-1825-0097', '0000-0002-1825-0097'];
        yield 'bare with X' => ['0000-0002-1694-233X', '0000-0002-1694-233X'];
        yield 'lowercase x' => ['0000-0002-1694-233x', '0000-0002-1694-233X'];
        yield 'third example' => ['0000-0001-5109-3700', '0000-0001-5109-3700'];
        yield 'https URL' => ['https://orcid.org/0000-0002-1825-0097', '0000-0002-1825-0097'];
        yield 'http URL' => ['http://orcid.org/0000-0002-1825-0097', '0000-0002-1825-0097'];
        yield 'www URL' => ['https://www.orcid.org/0000-0002-1825-0097', '0000-0002-1825-0097'];
        yield 'uppercase URL' => ['HTTPS://ORCID.ORG/0000-0002-1825-0097', '0000-0002-1825-0097'];
        yield 'without hyphens' => ['0000000218250097', '0000-0002-1825-0097'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheHyphenatedForm(string $input, string $expected): void
    {
        self::assertSame($expected, (new Orcid($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check' => ['0000-0002-1825-0098'];
        yield 'misplaced hyphens' => ['00000-002-1825-0097'];
        yield 'partial hyphens' => ['0000-00021825-0097'];
        yield 'spaces' => ['0000 0002 1825 0097'];
        yield 'other host' => ['https://example.org/0000-0002-1825-0097'];
        yield 'trailing path' => ['https://orcid.org/0000-0002-1825-0097/works'];
        yield 'fifteen digits' => ['0000-0002-1825-009'];
        yield 'trailing newline' => ["0000-0002-1825-0097\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Orcid($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Orcid('0000-0002-1825-0098');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ORCID has a wrong check character.', $e->getMessage());
        }

        try {
            new Orcid('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ORCID has an invalid format.', $e->getMessage());
        }
    }

    public function testUriAndIsni(): void
    {
        $orcid = new Orcid('0000-0002-1825-0097');

        self::assertSame('https://orcid.org/0000-0002-1825-0097', $orcid->uri());
        self::assertSame('0000000218250097', $orcid->toIsni()->value);
    }
}
