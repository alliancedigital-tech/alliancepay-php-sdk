<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Order;

use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;
use Exception;

/**
 * Class OrderRequestCompletionDTO.
 */
final class OrderRequestCompletionDTO
{
    private const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';

    private const PROPERTY_MERCHANT_ID = 'merchantId';

    private const PROPERTY_ORIGINAL_OPERATION_ID = 'originalOperationId';

    private const PROPERTY_COIN_AMOUNT = 'coinAmount';

    private const PROPERTY_DATE = 'date';

    private const PROPERTY_NOTIFICATION_URL = 'notificationUrl';

    private const DEFAULT_REQUEST_COMPLETION_DATE_TIME_FORMAT = 'Y-m-d H:i:s.sP';

    public function __construct(
        private string $merchantRequestId,
        private string $merchantId,
        private string $originalOperationId,
        private int $coinAmount,
        private DateTimeImmutable $date,
        private string $notificationUrl,
    ) {}

    /**
     * @return string
     */
    public function getMerchantRequestId(): string
    {
        return $this->merchantRequestId;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return string
     */
    public function getOriginalOperationId(): string
    {
        return $this->originalOperationId;
    }

    /**
     * @return int
     */
    public function getCoinAmount(): int
    {
        return $this->coinAmount;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getNotificationUrl(): string
    {
        return $this->notificationUrl;
    }

    /**
     * @param array $data
     * @return self
     * @throws Exception
     */
    public static function fromArray(array $data): self
    {
        return new self(
            merchantRequestId: $data[self::PROPERTY_MERCHANT_REQUEST_ID],
            merchantId: $data[self::PROPERTY_MERCHANT_ID],
            originalOperationId: $data[self::PROPERTY_ORIGINAL_OPERATION_ID],
            coinAmount: (int)$data[self::PROPERTY_COIN_AMOUNT],
            date: DateTimeImmutableProvider::fromString($data[self::PROPERTY_DATE], DateTimeImmutableProvider::DEFAULT_TIMEZONE),
            notificationUrl: $data[self::PROPERTY_NOTIFICATION_URL],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_ORIGINAL_OPERATION_ID => $this->originalOperationId,
            self::PROPERTY_COIN_AMOUNT => $this->coinAmount,
            self::PROPERTY_DATE => $this->date->format(self::DEFAULT_REQUEST_COMPLETION_DATE_TIME_FORMAT),
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl,
        ];
    }
}
