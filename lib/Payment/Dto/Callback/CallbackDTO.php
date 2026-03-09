<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationRefundDTO;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;

/**
 * Class CallbackDTO.
 */
final class CallbackDTO
{
    private const PROPERTY_ECOM_ORDER_ID = 'ecomOrderId';
    private const PROPERTY_COIN_AMOUNT = 'coinAmount';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_STATUS_URL = 'statusUrl';
    private const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    private const PROPERTY_REDIRECT_URL = 'redirectUrl';
    private const PROPERTY_NOTIFICATION_URL = 'notificationUrl';
    private const PROPERTY_NOTIFICATION_ENCRYPTION = 'notificationEncryption';
    private const PROPERTY_HPP_PAY_TYPE = 'hppPayType';
    private const PROPERTY_HPP_DIRECT_TYPE = 'hppDirectType';
    private const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    private const PROPERTY_CREATE_DATE = 'createDate';
    private const PROPERTY_PAYMENT_METHODS = 'paymentMethods';
    private const PROPERTY_ORDER_STATUS = 'orderStatus';
    private const PROPERTY_EXPIRED_ORDER_DATE = 'expiredOrderDate';
    private const PROPERTY_OPERATION = 'operation';

    public function __construct(
        private string $ecomOrderId,
        private int $coinAmount,
        private string $merchantId,
        private string $statusUrl,
        private string $redirectUrl,
        private string $notificationUrl,
        private bool $notificationEncryption,
        private string $hppOrderId,
        private string $hppDirectType,
        private string $merchantRequestId,
        private ?DateTimeImmutable $createDate,
        private array $paymentMethods,
        private string $orderStatus,
        private ?DateTimeImmutable $expiredOrderDate,
        private OperationPurchaseDTO|OperationRefundDTO $operation
    ) {}

    public static function fromArray(array $data): self
    {
        if (!empty($data[self::PROPERTY_OPERATION])
            && is_array($data[self::PROPERTY_OPERATION])
        ) {
            $operation = self::applyOperationObject($data[self::PROPERTY_OPERATION]);
        }

        return new self(
            ecomOrderId: $data[self::PROPERTY_ECOM_ORDER_ID],
            coinAmount: $data[self::PROPERTY_COIN_AMOUNT],
            merchantId: $data[self::PROPERTY_MERCHANT_ID],
            statusUrl: $data[self::PROPERTY_STATUS_URL],
            redirectUrl: $data[self::PROPERTY_REDIRECT_URL],
            notificationUrl: $data[self::PROPERTY_NOTIFICATION_URL],
            notificationEncryption: $data[self::PROPERTY_NOTIFICATION_ENCRYPTION] ?? false,
            hppOrderId: $data[self::PROPERTY_HPP_ORDER_ID],
            hppDirectType: $data[self::PROPERTY_HPP_DIRECT_TYPE] ?? '',
            merchantRequestId: $data[self::PROPERTY_MERCHANT_REQUEST_ID],
            createDate: !empty($data[self::PROPERTY_CREATE_DATE])
                ? DateTimeImmutableProvider::fromString(
                    $data[self::PROPERTY_CREATE_DATE],
                    DateTimeImmutableProvider::DEFAULT_TIMEZONE
                ) : null,
            paymentMethods: $data[self::PROPERTY_PAYMENT_METHODS],
            orderStatus: $data[self::PROPERTY_ORDER_STATUS],
            expiredOrderDate: !empty($data[self::PROPERTY_EXPIRED_ORDER_DATE])
                ? DateTimeImmutableProvider::fromString(
                    $data[self::PROPERTY_EXPIRED_ORDER_DATE],
                    DateTimeImmutableProvider::DEFAULT_TIMEZONE
                ) : null,
            operation: $operation
        );
    }

    public function toArray(): array
    {
        return [
            self::PROPERTY_ECOM_ORDER_ID => $this->ecomOrderId,
            self::PROPERTY_COIN_AMOUNT => $this->coinAmount,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_STATUS_URL => $this->statusUrl,
            self::PROPERTY_HPP_ORDER_ID => $this->hppOrderId,
            self::PROPERTY_REDIRECT_URL => $this->redirectUrl,
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl,
            self::PROPERTY_NOTIFICATION_ENCRYPTION => $this->notificationEncryption,
            self::PROPERTY_HPP_PAY_TYPE => $this->hppOrderId,
            self::PROPERTY_HPP_DIRECT_TYPE => $this->hppDirectType,
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_CREATE_DATE => $this->createDate,
            self::PROPERTY_PAYMENT_METHODS => $this->paymentMethods,
            self::PROPERTY_ORDER_STATUS => $this->orderStatus,
            self::PROPERTY_EXPIRED_ORDER_DATE => $this->expiredOrderDate,
            self::PROPERTY_OPERATION => $this->operation,
        ];
    }

    /**
     * @param array $operation
     * @return OperationPurchaseDTO|OperationRefundDTO
     * @throws CallbackException
     */
    private static function applyOperationObject(array $operation): OperationPurchaseDTO|OperationRefundDTO
    {
        $type = $operation[OperationPurchaseDTO::PROPERTY_TYPE]
            ?? throw new CallbackException('Operation type is missing');

        return match ($type) {
            OperationPurchaseDTO::OPERATION_TYPE => OperationPurchaseDTO::fromArray($operation),
            OperationRefundDTO::OPERATION_TYPE   => OperationRefundDTO::fromArray($operation),
            default => throw new CallbackException('Unknown operation type: ' . $type),
        };
    }
}
