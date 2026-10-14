<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class PlanRuleDTO.
 */
final class PlanRuleDTO
{
    private const PROPERTY_PAYMENT_NUMBER_FROM = 'paymentNumberFrom';
    private const PROPERTY_PAYMENT_NUMBER_TO = 'paymentNumberTo';
    private const PROPERTY_INTERVAL_VALUE = 'intervalValue';
    private const PROPERTY_INTERVAL_UNIT = 'intervalUnit';
    private const PROPERTY_COIN_AMOUNT = 'coinAmount';
    private const PROPERTY_CURRENCY = 'currency';

    public const CURRENCY_UAH = '980';
    public const SUPPORTED_CURRENCIES = [self::CURRENCY_UAH];

    public function __construct(
        private int $paymentNumberFrom,
        private int $paymentNumberTo,
        private int $intervalValue,
        private string $intervalUnit,
        private int $coinAmount,
        private string $currency = self::CURRENCY_UAH,
    ) {
    }

    /**
     * @return int
     */
    public function getPaymentNumberFrom(): int
    {
        return $this->paymentNumberFrom;
    }

    /**
     * @return int
     */
    public function getPaymentNumberTo(): int
    {
        return $this->paymentNumberTo;
    }

    /**
     * @return int
     */
    public function getIntervalValue(): int
    {
        return $this->intervalValue;
    }

    /**
     * @return string
     */
    public function getIntervalUnit(): string
    {
        return $this->intervalUnit;
    }

    /**
     * @return int
     */
    public function getCoinAmount(): int
    {
        return $this->coinAmount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            paymentNumberFrom: (int)$data[self::PROPERTY_PAYMENT_NUMBER_FROM],
            paymentNumberTo: (int)$data[self::PROPERTY_PAYMENT_NUMBER_TO],
            intervalValue: (int)$data[self::PROPERTY_INTERVAL_VALUE],
            intervalUnit: $data[self::PROPERTY_INTERVAL_UNIT],
            coinAmount: (int)$data[self::PROPERTY_COIN_AMOUNT],
            currency: (string)($data[self::PROPERTY_CURRENCY] ?? self::CURRENCY_UAH),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_PAYMENT_NUMBER_FROM => $this->paymentNumberFrom,
            self::PROPERTY_PAYMENT_NUMBER_TO => $this->paymentNumberTo,
            self::PROPERTY_INTERVAL_VALUE => $this->intervalValue,
            self::PROPERTY_INTERVAL_UNIT => $this->intervalUnit,
            self::PROPERTY_COIN_AMOUNT => $this->coinAmount,
            self::PROPERTY_CURRENCY => $this->currency,
        ];
    }
}
