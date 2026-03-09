<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Order;

use AlliancePay\Sdk\Exceptions\AlliancePayException;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationRefundDTO;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;
use Exception;

/**
 * Class OrderDataResponseDTO.
 */
final class OrderDataResponseDTO
{
    public const PROPERTY_COIN_AMOUNT = 'coinAmount';
    public const PROPERTY_ECOM_ORDER_ID = 'ecomOrderId';
    public const PROPERTY_STATUS_URL = 'statusUrl';
    public const PROPERTY_MERCHANT_ID = 'merchantId';
    public const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    public const PROPERTY_REDIRECT_URL = 'redirectUrl';
    public const PROPERTY_HPP_PAY_TYPE = 'hppPayType';
    public const PROPERTY_NOTIFICATION_URL = 'notificationUrl';
    public const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    public const PROPERTY_EXPIRED_ORDER_DATE = 'expiredOrderDate';
    public const PROPERTY_ORDER_STATUS = 'orderStatus';
    public const PROPERTY_PAYMENT_METHODS = 'paymentMethods';
    public const PROPERTY_CREATE_DATE = 'createDate';
    public const PROPERTY_OPERATIONS = 'operations';

    public function __construct(
        private int $coinAmount,
        private string $ecomOrderId,
        private ?string $statusUrl,
        private string $merchantId,
        private string $hppOrderId,
        private ?string $redirectUrl,
        private string $hppPayType,
        private ?string $notificationUrl,
        private string $merchantRequestId,
        private ?DateTimeImmutable $expiredOrderDate,
        private string $orderStatus,
        private array $paymentMethods,
        private ?DateTimeImmutable $createDate,
        private array $operations
    ) {}

    /**
     * @param array $data
     * @return self
     * @throws Exception
     */
    public static function fromArray(array $data): self
    {
        return new self(
            coinAmount: (int)($data[self::PROPERTY_COIN_AMOUNT] ?? 0),
            ecomOrderId: $data[self::PROPERTY_ECOM_ORDER_ID] ?? '',
            statusUrl: $data[self::PROPERTY_STATUS_URL] ?? null,
            merchantId: $data[self::PROPERTY_MERCHANT_ID] ?? '',
            hppOrderId: $data[self::PROPERTY_HPP_ORDER_ID] ?? '',
            redirectUrl: $data[self::PROPERTY_REDIRECT_URL] ?? null,
            hppPayType: $data[self::PROPERTY_HPP_PAY_TYPE] ?? '',
            notificationUrl: $data[self::PROPERTY_NOTIFICATION_URL] ?? null,
            merchantRequestId: $data[self::PROPERTY_MERCHANT_REQUEST_ID] ?? '',
            expiredOrderDate: DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_EXPIRED_ORDER_DATE],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            orderStatus: $data[self::PROPERTY_ORDER_STATUS] ?? '',
            paymentMethods: $data[self::PROPERTY_PAYMENT_METHODS] ?? [],
            createDate: DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_CREATE_DATE],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            operations: self::applyOperations($data[self::PROPERTY_OPERATIONS]) ?? []
        );
    }

    /**
     * @param array $operations
     * @return array
     * @throws AlliancePayException
     */
    private static function applyOperations(array $operations): array
    {
        $preparedOperations = [];

        foreach ($operations as $operation) {
            $type = $operation[OperationPurchaseDTO::PROPERTY_TYPE]
                ?? throw new AlliancePayException('Operation type is missing');

            $preparedOperations[] = match ($type) {
                OperationPurchaseDTO::OPERATION_TYPE => OperationPurchaseDTO::fromArray($operation),
                OperationRefundDTO::OPERATION_TYPE   => OperationRefundDTO::fromArray($operation),
                default => throw new AlliancePayException('Unknown operation type: ' . $type),
            };
        }

        return $preparedOperations;
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
    public function getEcomOrderId(): string
    {
        return $this->ecomOrderId;
    }

    /**
     * @return string|null
     */
    public function getStatusUrl(): ?string
    {
        return $this->statusUrl;
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
    public function getHppOrderId(): string
    {
        return $this->hppOrderId;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    /**
     * @return string
     */
    public function getHppPayType(): string
    {
        return $this->hppPayType;
    }

    /**
     * @return string|null
     */
    public function getNotificationUrl(): ?string
    {
        return $this->notificationUrl;
    }

    /**
     * @return string
     */
    public function getMerchantRequestId(): string
    {
        return $this->merchantRequestId;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getExpiredOrderDate(): ?DateTimeImmutable
    {
        return $this->expiredOrderDate;
    }

    /**
     * @return string
     */
    public function getOrderStatus(): string
    {
        return $this->orderStatus;
    }

    /**
     * @return array
     */
    public function getPaymentMethods(): array
    {
        return $this->paymentMethods;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreateDate(): ?DateTimeImmutable
    {
        return $this->createDate;
    }

    /**
     * @return array
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_COIN_AMOUNT => $this->coinAmount,
            self::PROPERTY_ECOM_ORDER_ID => $this->ecomOrderId,
            self::PROPERTY_STATUS_URL => $this->statusUrl,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_HPP_ORDER_ID => $this->hppOrderId,
            self::PROPERTY_REDIRECT_URL => $this->redirectUrl,
            self::PROPERTY_HPP_PAY_TYPE => $this->hppPayType,
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl,
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_EXPIRED_ORDER_DATE =>
                $this->expiredOrderDate?->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT),
            self::PROPERTY_ORDER_STATUS => $this->orderStatus,
            self::PROPERTY_PAYMENT_METHODS => $this->paymentMethods,
            self::PROPERTY_CREATE_DATE =>
                $this->createDate?->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT),
            self::PROPERTY_OPERATIONS => $this->operations,
        ];
    }
}
