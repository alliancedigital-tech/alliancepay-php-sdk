<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Callback;

/**
 * Class ScheduledPaymentDTO.
 *
 * One entry of a subscription callback's `scheduledPayments` array. Best-effort field list — the source
 * specification only says this array "may include the entire future schedule" without a field list, and no
 * real captured callback payload was available (see plan's open question №1). Every field is nullable and
 * `fromArray()` never throws on an unknown shape, so a mismatch here never blocks processing of the rest
 * of the callback.
 */
final class ScheduledPaymentDTO
{
    private const PROPERTY_SCHEDULED_PAYMENT_ID = 'scheduledPaymentId';
    private const PROPERTY_PAYMENT_NUMBER = 'paymentNumber';
    private const PROPERTY_SCHEDULED_DATE_TIME = 'scheduledDateTime';
    private const PROPERTY_COIN_AMOUNT = 'coinAmount';
    private const PROPERTY_CURRENCY = 'currency';
    private const PROPERTY_STATUS = 'status';

    public function __construct(
        private ?string $scheduledPaymentId,
        private ?int $paymentNumber,
        private ?string $scheduledDateTime,
        private ?int $coinAmount,
        private ?string $currency,
        private ?string $status,
    ) {
    }

    /**
     * @return string|null
     */
    public function getScheduledPaymentId(): ?string
    {
        return $this->scheduledPaymentId;
    }

    /**
     * @return int|null
     */
    public function getPaymentNumber(): ?int
    {
        return $this->paymentNumber;
    }

    /**
     * @return string|null
     */
    public function getScheduledDateTime(): ?string
    {
        return $this->scheduledDateTime;
    }

    /**
     * @return int|null
     */
    public function getCoinAmount(): ?int
    {
        return $this->coinAmount;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            scheduledPaymentId: isset($data[self::PROPERTY_SCHEDULED_PAYMENT_ID])
                ? (string)$data[self::PROPERTY_SCHEDULED_PAYMENT_ID] : null,
            paymentNumber: isset($data[self::PROPERTY_PAYMENT_NUMBER])
                ? (int)$data[self::PROPERTY_PAYMENT_NUMBER] : null,
            scheduledDateTime: isset($data[self::PROPERTY_SCHEDULED_DATE_TIME])
                ? (string)$data[self::PROPERTY_SCHEDULED_DATE_TIME] : null,
            coinAmount: isset($data[self::PROPERTY_COIN_AMOUNT])
                ? (int)$data[self::PROPERTY_COIN_AMOUNT] : null,
            currency: isset($data[self::PROPERTY_CURRENCY])
                ? (string)$data[self::PROPERTY_CURRENCY] : null,
            status: isset($data[self::PROPERTY_STATUS])
                ? (string)$data[self::PROPERTY_STATUS] : null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_SCHEDULED_PAYMENT_ID => $this->scheduledPaymentId,
            self::PROPERTY_PAYMENT_NUMBER => $this->paymentNumber,
            self::PROPERTY_SCHEDULED_DATE_TIME => $this->scheduledDateTime,
            self::PROPERTY_COIN_AMOUNT => $this->coinAmount,
            self::PROPERTY_CURRENCY => $this->currency,
            self::PROPERTY_STATUS => $this->status,
        ];
    }
}
