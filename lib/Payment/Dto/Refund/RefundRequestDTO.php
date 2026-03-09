<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Refund;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Refund\Validation\ValidateRefundData;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;

/**
 * Class RefundRequestDTO.
 */
final class RefundRequestDTO
{
    private const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_OPERATION_ID = 'operationId';
    private const PROPERTY_COIN_AMOUNT = 'coinAmount';
    private const PROPERTY_DATE = 'date';
    private const PROPERTY_NOTIFICATION_URL = 'notificationUrl';
    private const PROPERTY_NOTIFICATION_ENCRYPTION = 'notificationEncryption';
    private const PROPERTY_MERCHANT_COMMENT = 'merchantComment';
    private const DEFAULT_REQUEST_REFUND_DATE_TIME_FORMAT = 'Y-m-d H:i:s.sP';

    public function __construct(
        private string $merchantRequestId,
        private string $merchantId,
        private string $operationId,
        private int $coinAmount,
        private DateTimeImmutable $date,
        private string $notificationUrl = '',
        private string $notificationEncryption = '',
        private string $merchantComment = ''
    ) {

    }

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
    public function getOperationId(): string
    {
        return $this->operationId;
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
     * @return string
     */
    public function getNotificationEncryption(): string
    {
        return $this->notificationEncryption;
    }

    /**
     * @return string
     */
    public function getMerchantComment(): string
    {
        return $this->merchantComment;
    }

    /**
     * @param array $refundData
     * @return self
     * @throws ValidateDataException
     */
    public static function fromArray(array $refundData): self
    {
        $refundData = ValidateRefundData::validateRefundRequiredData($refundData);

        return new self(
            merchantRequestId: $refundData[self::PROPERTY_MERCHANT_REQUEST_ID],
            merchantId: $refundData[self::PROPERTY_MERCHANT_ID],
            operationId: $refundData[self::PROPERTY_OPERATION_ID],
            coinAmount: $refundData[self::PROPERTY_COIN_AMOUNT],
            date: $refundData[self::PROPERTY_DATE],
            notificationUrl: $refundData[self::PROPERTY_NOTIFICATION_URL] ?? '',
            notificationEncryption: $refundData[self::PROPERTY_NOTIFICATION_ENCRYPTION] ?? '',
            merchantComment: $refundData[self::PROPERTY_MERCHANT_COMMENT] ?? ''
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $refundData = [
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_OPERATION_ID => $this->operationId,
            self::PROPERTY_COIN_AMOUNT => $this->coinAmount,
            self::PROPERTY_DATE => $this->date->format(self::DEFAULT_REQUEST_REFUND_DATE_TIME_FORMAT),
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl ?? '',
            self::PROPERTY_NOTIFICATION_ENCRYPTION => $this->notificationEncryption ?? '',
            self::PROPERTY_MERCHANT_COMMENT => $this->merchantComment ?? ''
        ];

        return ValidateRefundData::clearRefundData($refundData);
    }
}
