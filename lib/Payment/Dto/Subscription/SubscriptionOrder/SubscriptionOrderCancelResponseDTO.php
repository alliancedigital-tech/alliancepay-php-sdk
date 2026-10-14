<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder;

/**
 * Class SubscriptionOrderCancelResponseDTO.
 *
 * Response shape is not confirmed by a real captured response (see plan's open question №1) — modeled
 * as a best-effort echo of `hppOrderId`/`status`, matching the shape of other bank ack responses in this
 * module (e.g. {@see \AlliancePay\Sdk\Payment\Dto\Subscription\Plan\PlanStatusUpdateResponseDTO}).
 */
final class SubscriptionOrderCancelResponseDTO
{
    private const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    private const PROPERTY_STATUS = 'status';

    public function __construct(
        private string $hppOrderId,
        private string $status,
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
    public function getStatus(): string
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
            hppOrderId: $data[self::PROPERTY_HPP_ORDER_ID],
            status: $data[self::PROPERTY_STATUS] ?? '',
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_HPP_ORDER_ID => $this->hppOrderId,
            self::PROPERTY_STATUS => $this->status,
        ];
    }
}
