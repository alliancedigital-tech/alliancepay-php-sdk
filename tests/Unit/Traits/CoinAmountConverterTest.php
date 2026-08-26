<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Traits;

use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;
use AlliancePay\Sdk\Traits\CoinAmountConverter;
use PHPUnit\Framework\TestCase;

class CoinAmountConverterTest extends TestCase
{
    use CoinAmountConverter;

    /**
     * @return void
     */
    public function test_convert_to_uah_coins_with_usd_amount(): void
    {
        $amount = 10.00;
        $conversionRate = 39.50;

        $result = self::convertToUahCoins($amount, $conversionRate);

        $this->assertSame(39500, $result);
    }

    /**
     * @return void
     */
    public function test_convert_to_uah_coins_with_eur_amount(): void
    {
        $amount = 25.50;
        $conversionRate = 42.75;

        $result = self::convertToUahCoins($amount, $conversionRate);

        $this->assertSame(109013, $result);
    }

    /**
     * @return void
     */
    public function test_convert_to_uah_coins_rounds_correctly(): void
    {
        $amount = 10.555;
        $conversionRate = 40.123;

        $result = self::convertToUahCoins($amount, $conversionRate);

        $this->assertSame(42350, $result);
    }

    /**
     * @return void
     */
    public function test_convert_to_uah_coins_with_zero_amount(): void
    {
        $amount = 0.00;
        $conversionRate = 39.50;

        $result = self::convertToUahCoins($amount, $conversionRate);

        $this->assertSame(0, $result);
    }

    /**
     * @return void
     */
    public function test_convert_to_uah_coins_with_small_amount(): void
    {
        $amount = 0.01;
        $conversionRate = 39.50;

        $result = self::convertToUahCoins($amount, $conversionRate);

        $this->assertSame(40, $result);
    }

    /**
     * @return void
     */
    public function test_convert_from_uah_coins_to_hryvnias(): void
    {
        $uahKopecks = 39500;

        $result = self::convertFromUahCoins($uahKopecks);

        $this->assertSame(395.00, $result);
    }

    /**
     * @return void
     */
    public function test_convert_from_uah_coins_with_zero(): void
    {
        $uahKopecks = 0;

        $result = self::convertFromUahCoins($uahKopecks);

        $this->assertSame(0.00, $result);
    }

    /**
     * @return void
     */
    public function test_convert_from_uah_coins_with_odd_amount(): void
    {
        $uahKopecks = 12345;

        $result = self::convertFromUahCoins($uahKopecks);

        $this->assertSame(123.45, $result);
    }

    /**
     * @return void
     */
    public function test_convert_from_uah_coins_rounds_to_two_decimals(): void
    {
        $uahKopecks = 1;

        $result = self::convertFromUahCoins($uahKopecks);

        $this->assertSame(0.01, $result);
    }

    /**
     * @return void
     */
    public function test_currency_code_from_alpha_uah(): void
    {
        $result = self::currencyCodeFromAlpha('UAH');

        $this->assertSame(OrderRequestDTO::CURRENCY_UAH, $result);
        $this->assertSame(980, $result);
    }

    /**
     * @return void
     */
    public function test_currency_code_from_alpha_usd(): void
    {
        $result = self::currencyCodeFromAlpha('USD');

        $this->assertSame(OrderRequestDTO::CURRENCY_USD, $result);
        $this->assertSame(840, $result);
    }

    /**
     * @return void
     */
    public function test_currency_code_from_alpha_eur(): void
    {
        $result = self::currencyCodeFromAlpha('EUR');

        $this->assertSame(OrderRequestDTO::CURRENCY_EUR, $result);
        $this->assertSame(978, $result);
    }

    /**
     * @return void
     */
    public function test_currency_code_from_alpha_lowercase(): void
    {
        $result = self::currencyCodeFromAlpha('usd');

        $this->assertSame(OrderRequestDTO::CURRENCY_USD, $result);
        $this->assertSame(840, $result);
    }

    /**
     * @return void
     */
    public function test_currency_code_from_alpha_mixed_case(): void
    {
        $result = self::currencyCodeFromAlpha('EuR');

        $this->assertSame(OrderRequestDTO::CURRENCY_EUR, $result);
        $this->assertSame(978, $result);
    }

    /**
     * @return void
     */
    public function test_currency_code_from_alpha_unsupported_returns_null(): void
    {
        $result = self::currencyCodeFromAlpha('GBP');

        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function test_currency_code_from_alpha_invalid_code_returns_null(): void
    {
        $result = self::currencyCodeFromAlpha('XXX');

        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function test_round_trip_conversion(): void
    {
        $originalAmount = 100.00;
        $conversionRate = 40.00;

        $kopecks = self::convertToUahCoins($originalAmount, $conversionRate);
        $uahAmount = self::convertFromUahCoins($kopecks);

        $this->assertSame(400000, $kopecks);
        $this->assertSame(4000.00, $uahAmount);
    }
}
