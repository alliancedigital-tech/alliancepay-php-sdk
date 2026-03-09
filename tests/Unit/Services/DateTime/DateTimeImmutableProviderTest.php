<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Services;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;

/**
 * Class DateTimeImmutableProviderTest.
 */
class DateTimeImmutableProviderTest extends TestCase
{
    /**
     * @dataProvider dateFormatsProvider
     */
    public function test_it_parses_various_alliance_pay_formats(string $input, string $expectedDate): void
    {
        $result = DateTimeImmutableProvider::fromString($input, DateTimeImmutableProvider::DEFAULT_TIMEZONE);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertEquals($expectedDate, $result->format('Y-m-d H:i:s'));
    }

    /**
     * @return array[]
     */
    public static function dateFormatsProvider(): array
    {
        return [
            'Format with dots between date parts (Callback)' => ['2026.02.13 10:06:01.927', '2026-02-13 10:06:01'],
            'Format with timezone (Token)' => ['2026-02-14 09:40:21.772+02:00', '2026-02-14 09:40:21'],
            'Format with space before timezone' => ['2026-02-14 07:40:21.0772 +0000', '2026-02-14 07:40:21'],
        ];
    }
}
