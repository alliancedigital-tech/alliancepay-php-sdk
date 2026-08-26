<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Traits;

use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;

/**
 * Trait CoinAmountConverter.
 *
 * Provides a utility for converting amounts in foreign currencies (USD, EUR)
 * to UAH kopecks (the internal unit used by coinAmount fields across the SDK).
 *
 * Usage example before calling RefundOrder::createRefund() or
 * CreateOrderCompletion::createCompletion() when the original order was placed
 * in a non-UAH currency:
 *
 *   use AlliancePay\Sdk\Traits\CoinAmountConverter;
 *
 *   $uahKopecks = CoinAmountConverter::convertToUahCoins(
 *       amount: 10.00,
 *       conversionRate: 39.50 *);
 *   // $uahKopecks === 39500
 *
 *   $refundDTO = new RefundRequestDTO(
 *       coinAmount: $uahKopecks,
 *       ... *);
 */
trait CoinAmountConverter
{
    /**
     * Converts an amount in a source currency to UAH kopecks.
     *
     * The formula is: round (amount × conversionRate × 100)
     *   - amount — value in the source currency (e.g. 10.00 USD)
     *   - conversionRate — how many UAH equal 1 unit of source currency (e.g. 39.50)
     *   - × 100 — converts UAH to kopecks (minor currency units)
     *
     * @param float $amount Amount in source currency (e.g. USD or EUR).
     * @param float $conversionRate Exchange rate: 1 source currency unit = N UAH.
     * @return int                 Amount in UAH kopecks, ready for coinAmount fields.
     */
    public static function convertToUahCoins(float $amount, float $conversionRate): int
    {
        return (int)round($amount * $conversionRate * 100);
    }

    /**
     * Converts a UAH kopeck amount to UAH major units (hryvnias).
     *
     * Convenience reverse helper — useful for display purposes.
     *
     * @param int $uahKopecks Amount in UAH kopecks (coinAmount).
     * @return float Amount in UAH (e.g. 39500 → 395.00).
     */
    public static function convertFromUahCoins(int $uahKopecks): float
    {
        return round($uahKopecks / 100, 2);
    }

    /**
     * Returns the ISO 4217 numeric currency code by its alphabetic code.
     * Uses the currency map defined in OrderRequestDTO.
     *
     * @param string $alphabeticCode Alphabetic ISO 4217 code (e.g. 'UAH', 'USD', 'EUR').
     * @return int|null              Numeric code (e.g. 980, 840, 978) or null if unsupported.
     */
    public static function currencyCodeFromAlpha(string $alphabeticCode): ?int
    {
        $map = [
            'UAH' => OrderRequestDTO::CURRENCY_UAH,
            'USD' => OrderRequestDTO::CURRENCY_USD,
            'EUR' => OrderRequestDTO::CURRENCY_EUR,
        ];

        return $map[strtoupper($alphabeticCode)] ?? null;
    }
}
