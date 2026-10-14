<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder;

/**
 * Class SubscriptionOrderCancelRequestDTO.
 *
 * Cancellation is resolved by the bank's unique order id (`hppOrderId`), never by a local subscription/plan
 * identifier (per the source specification, section 4.5).
 */
final class SubscriptionOrderCancelRequestDTO
{
    private const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';

    public function __construct(
        private string $hppOrderId,
        private string $merchantId,
        private ?string $merchantRequestId = null,
    ) {
    }

    /**
     * @return string
     */
    public function getHppOrderId(): string
    {
        return $this->hppOrderId;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return string|null
     */
    public function getMerchantRequestId(): ?string
    {
        return $this->merchantRequestId;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            hppOrderId: $data[self::PROPERTY_HPP_ORDER_ID],
            merchantId: $data[self::PROPERTY_MERCHANT_ID],
            merchantRequestId: $data[self::PROPERTY_MERCHANT_REQUEST_ID] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            self::PROPERTY_HPP_ORDER_ID => $this->hppOrderId,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
        ];

        if ($this->merchantRequestId !== null) {
            $data[self::PROPERTY_MERCHANT_REQUEST_ID] = $this->merchantRequestId;
        }

        return $data;
    }
}
