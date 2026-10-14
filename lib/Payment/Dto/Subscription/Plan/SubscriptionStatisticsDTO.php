<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Plan;

/**
 * Class SubscriptionStatisticsDTO.
 */
final class SubscriptionStatisticsDTO
{
    private const PROPERTY_PENDING = 'pending';
    private const PROPERTY_SUCCESS = 'success';
    private const PROPERTY_FAIL = 'fail';
    private const PROPERTY_CANCELED = 'canceled';

    public function __construct(
        private int $pending,
        private int $success,
        private int $fail,
        private int $canceled,
    ) {
    }

    /**
     * @return int
     */
    public function getPending(): int
    {
        return $this->pending;
    }

    /**
     * @return int
     */
    public function getSuccess(): int
    {
        return $this->success;
    }

    /**
     * @return int
     */
    public function getFail(): int
    {
        return $this->fail;
    }

    /**
     * @return int
     */
    public function getCanceled(): int
    {
        return $this->canceled;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            pending: (int)($data[self::PROPERTY_PENDING] ?? 0),
            success: (int)($data[self::PROPERTY_SUCCESS] ?? 0),
            fail: (int)($data[self::PROPERTY_FAIL] ?? 0),
            canceled: (int)($data[self::PROPERTY_CANCELED] ?? 0),
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_PENDING => $this->pending,
            self::PROPERTY_SUCCESS => $this->success,
            self::PROPERTY_FAIL => $this->fail,
            self::PROPERTY_CANCELED => $this->canceled,
        ];
    }
}
