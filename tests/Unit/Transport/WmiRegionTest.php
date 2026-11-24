<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Transport\WmiRegion;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class WmiRegionTest extends TestCase
{
    public function testBackedValuesAreFrozen(): void
    {
        self::assertSame(
            [
                'Africa' => 'africa',
                'Asia' => 'asia',
                'Europe' => 'europe',
                'NorthAmerica' => 'north-america',
                'Oceania' => 'oceania',
                'SouthAmerica' => 'south-america',
            ],
            \array_column(\array_map(static fn (WmiRegion $case): array => [$case->name, $case->value], WmiRegion::cases()), 1, 0),
        );
    }
}
