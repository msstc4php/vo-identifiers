<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod11_2;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isni;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Isni::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Iso7064Mod11_2::class)]
final class IsniTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'ISO 27729 example printed' => ['ISNI 1422 4586 3573 0476', '1422458635730476'];
        yield 'compact' => ['1422458635730476', '1422458635730476'];
        yield 'hyphens' => ['1422-4586-3573-0476', '1422458635730476'];
        yield 'ORCID-form with X' => ['0000-0002-1694-233x', '000000021694233X'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheCompactForm(string $input, string $expected): void
    {
        self::assertSame($expected, (new Isni($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check' => ['1422 4586 3573 0477'];
        yield 'X inside' => ['1422 4586 357X 0476'];
        yield 'fifteen digits' => ['142245863573047'];
        yield 'seventeen digits' => ['14224586357304761'];
        yield 'ORCID URL' => ['https://orcid.org/0000-0002-1825-0097'];
        yield 'trailing newline' => ["1422458635730476\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Isni($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Isni('1422 4586 3573 0477');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISNI has a wrong check character.', $e->getMessage());
        }

        try {
            new Isni('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISNI has an invalid format.', $e->getMessage());
        }
    }

    public function testFormatted(): void
    {
        self::assertSame('ISNI 1422 4586 3573 0476', new Isni('1422458635730476')->formatted());
    }
}
