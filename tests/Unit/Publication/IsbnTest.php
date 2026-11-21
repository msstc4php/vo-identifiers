<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Gs1\Key\Gtin;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Ean13CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Mod11CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isbn;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Isbn::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Ean13CheckDigit::class)]
#[UsesClass(Mod11CheckDigit::class)]
final class IsbnTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'ISBN-10 textbook' => ['0-306-40615-2', '0306406152'];
        yield 'ISBN-10 agency example' => ['0-8436-1072-7', '0843610727'];
        yield 'ISBN-10 with X' => ['0-9752298-0-X', '097522980X'];
        yield 'ISBN-10 lowercase x' => ['0-9752298-0-x', '097522980X'];
        yield 'ISBN-13 textbook' => ['978-0-306-40615-7', '9780306406157'];
        yield 'ISBN-13 agency 7th edition' => ['978-92-95055-12-4', '9789295055124'];
        yield 'ISBN-13 agency 6th edition' => ['978-0-11-000222-4', '9780110002224'];
        yield 'ISBN-13 979' => ['979-10-343-0062-4', '9791034300624'];
        yield 'spaces' => ['978 0 306 40615 7', '9780306406157'];
        yield 'printed with label' => ['ISBN 978-0-306-40615-7', '9780306406157'];
        yield 'label with colon' => ['isbn: 0-306-40615-2', '0306406152'];
        yield 'label ISBN-13' => ['ISBN-13: 978-0-306-40615-7', '9780306406157'];
        yield 'label ISBN-10' => ['ISBN-10 0-306-40615-2', '0306406152'];
        yield 'label with colon and no space' => ['ISBN:9780306406157', '9780306406157'];
        yield 'compact' => ['9780306406157', '9780306406157'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheCompactForm(string $input, string $expected): void
    {
        self::assertSame($expected, (new Isbn($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'ISBN-10 wrong check' => ['0-306-40615-3'];
        yield 'ISBN-13 wrong check' => ['978-0-306-40615-6'];
        yield 'ISBN-10 X in the middle' => ['0-30X-40615-2'];
        yield 'X in an ISBN-13' => ['978030640615X'];
        yield 'wrong prefix' => ['977-0-306-40615-5'];
        yield 'twelve digits' => ['978030640615'];
        yield 'eleven digits' => ['03064061523'];
        yield 'letters' => ['ABCDEFGHIJ'];
        yield 'tab separator' => ["978\t0306406157"];
        yield 'trailing newline' => ["9780306406157\n"];
        yield 'empty' => [''];
        yield 'label only' => ['ISBN '];
        yield 'label without a separator' => ['ISBN9780306406157'];
        yield 'label with another number' => ['ISBN-12: 9780306406157'];
        yield 'ISMN 979-0' => ['979-0-3452-4680-5'];
        yield 'ISMN compact' => ['9790345246805'];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Isbn($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Isbn('978-0-306-40615-6');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISBN has a wrong check digit.', $e->getMessage());
        }

        try {
            new Isbn('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISBN has an invalid format.', $e->getMessage());
        }
    }

    public function testTenToThirteen(): void
    {
        $isbn = new Isbn('0-306-40615-2');

        self::assertTrue($isbn->isIsbn10());
        self::assertSame('0306406152', $isbn->isbn10());
        self::assertSame('9780306406157', $isbn->isbn13());
        self::assertSame('9780306406157', $isbn->toGtin()->value);
    }

    public function testThirteenToTen(): void
    {
        $isbn = new Isbn('978-0-11-000222-4');

        self::assertFalse($isbn->isIsbn10());
        self::assertSame('0110002229', $isbn->isbn10());
        self::assertSame('9780110002224', $isbn->isbn13());
        self::assertSame('9780110002224', $isbn->toGtin()->value);
    }

    public function testTenWithXFromThirteen(): void
    {
        self::assertSame('097522980X', new Isbn('9780975229804')->isbn10());
    }

    public function testNineSevenNineHasNoIsbn10(): void
    {
        $isbn = new Isbn('979-10-343-0062-4');

        self::assertNull($isbn->isbn10());
        self::assertSame('9791034300624', $isbn->isbn13());
        self::assertSame('9791034300624', $isbn->toGtin()->value);
    }

    public function testBothFormsOfTheSameBookAreEqual(): void
    {
        self::assertTrue(new Isbn('0-306-40615-2')->equals(new Isbn('978-0-306-40615-7')));
        self::assertFalse(new Isbn('0-306-40615-2')->equals(new Isbn('978-92-95055-12-4')));
        self::assertFalse(new Isbn('0-306-40615-2')->equals(new Gtin('9780306406157')));
    }

    public function testStringAndJson(): void
    {
        $isbn = new Isbn('0-306-40615-2');

        self::assertSame('0306406152', (string) $isbn);
        self::assertSame('"0306406152"', \json_encode($isbn));
    }
}
