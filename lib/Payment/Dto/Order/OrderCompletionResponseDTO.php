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
 * Class OrderCompletionResponseDTO.
 */
final class OrderCompletionResponseDTO
{
    public const PROPERTY_TYPE = 'type';

    public const PROPERTY_RRN = 'rrn';

    public const PROPERTY_COIN_AMOUNT = 'coinAmount';

    public const PROPERTY_MERCHANT_ID = 'merchantId';

    public const PROPERTY_OPERATION_ID = 'operationId';

    public const PROPERTY_ECOM_OPERATION_ID = 'ecomOperationId';

    public const PROPERTY_STATUS = 'status';

    public const PROPERTY_TRANSACTION_TYPE = 'transactionType';

    public const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';

    public const PROPERTY_TRANSACTION_CURRENCY = 'transactionCurrency';

    public const PROPERTY_CREATION_DATE_TIME = 'creationDateTime';

    public const PROPERTY_MODIFICATION_DATE_TIME = 'modificationDateTime';

    public const PROPERTY_TRANSACTION_RESPONSE_INFO = 'transactionResponseInfo';

    public const PROPERTY_PRODUCT_TYPE = 'productType';

    public const PROPERTY_NOTIFICATION_URL = 'notificationUrl';

    public const PROPERTY_PAYMENT_SERVICE_TYPE = 'paymentServiceType';

    public const PROPERTY_NOTIFICATION_ENCRYPTION = 'notificationEncryption';

    public const PROPERTY_NOTIFICATION_SIGNATURE = 'notificationSignature';

    public const PROPERTY_HPP_ORDER_ID = 'hppOrderId';

    public const PROPERTY_PROCESSING_TERMINAL_ID = 'processingTerminalId';

    public const PROPERTY_PROCESSING_MERCHANT_ID = 'processingMerchantId';

    public const PROPERTY_CREATOR_SYSTEM = 'creatorSystem';

    public const PROPERTY_RRN_PREAUTH = 'rrnPreauth';

    public const PROPERTY_PREAUTH_OPERATION_ID = 'preauthOperationId';

    public const PROPERTY_PREAUTH_COIN_AMOUNT = 'preauthCoinAmount';

    public const PROPERTY_PREAUTH_ECOM_OPERATION_ID = 'preauthEcomOperationId';

    public function __construct(
        private string $type,
        private string $rrn,
        private int $coinAmount,
        private string $merchantId,
        private string $operationId,
        private string $ecomOperationId,
        private string $status,
        private ?int $transactionType,
        private ?string $merchantRequestId,
        private string $transactionCurrency,
        private ?DateTimeImmutable $creationDateTime,
        private ?DateTimeImmutable $modificationDateTime,
        private array $transactionResponseInfo,
        private ?string $productType,
        private ?string $notificationUrl,
        private ?string $paymentServiceType,
        private bool $notificationEncryption,
        private bool $notificationSignature,
        private string $hppOrderId,
        private ?string $processingTerminalId,
        private ?string $processingMerchantId,
        private ?string $creatorSystem,
        private ?string $rrnPreauth,
        private ?string $preauthOperationId,
        private ?int $preauthCoinAmount,
        private ?string $preauthEcomOperationId,
    ) {}

    /**
     * @param array $data
     * @return self
     * @throws Exception
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data[self::PROPERTY_TYPE] ?? '',
            rrn: $data[self::PROPERTY_RRN] ?? '',
            coinAmount: (int)($data[self::PROPERTY_COIN_AMOUNT] ?? 0),
            merchantId: $data[self::PROPERTY_MERCHANT_ID] ?? '',
            operationId: $data[self::PROPERTY_OPERATION_ID] ?? '',
            ecomOperationId: $data[self::PROPERTY_ECOM_OPERATION_ID] ?? '',
            status: $data[self::PROPERTY_STATUS] ?? '',
            transactionType: isset($data[self::PROPERTY_TRANSACTION_TYPE])
                ? (int)$data[self::PROPERTY_TRANSACTION_TYPE] : null,
            merchantRequestId: $data[self::PROPERTY_MERCHANT_REQUEST_ID] ?? null,
            transactionCurrency: $data[self::PROPERTY_TRANSACTION_CURRENCY] ?? '',
            creationDateTime: !empty($data[self::PROPERTY_CREATION_DATE_TIME])
                ? DateTimeImmutableProvider::fromString(
                    $data[self::PROPERTY_CREATION_DATE_TIME],
                    DateTimeImmutableProvider::DEFAULT_TIMEZONE
                ) : null,
            modificationDateTime: !empty($data[self::PROPERTY_MODIFICATION_DATE_TIME])
                ? DateTimeImmutableProvider::fromString(
                    $data[self::PROPERTY_MODIFICATION_DATE_TIME],
                    DateTimeImmutableProvider::DEFAULT_TIMEZONE
                ) : null,
            transactionResponseInfo: $data[self::PROPERTY_TRANSACTION_RESPONSE_INFO] ?? [],
            productType: $data[self::PROPERTY_PRODUCT_TYPE] ?? null,
            notificationUrl: $data[self::PROPERTY_NOTIFICATION_URL] ?? null,
            paymentServiceType: $data[self::PROPERTY_PAYMENT_SERVICE_TYPE] ?? null,
            notificationEncryption: (bool)($data[self::PROPERTY_NOTIFICATION_ENCRYPTION] ?? false),
            notificationSignature: (bool)($data[self::PROPERTY_NOTIFICATION_SIGNATURE] ?? false),
            hppOrderId: $data[self::PROPERTY_HPP_ORDER_ID] ?? '',
            processingTerminalId: $data[self::PROPERTY_PROCESSING_TERMINAL_ID] ?? null,
            processingMerchantId: $data[self::PROPERTY_PROCESSING_MERCHANT_ID] ?? null,
            creatorSystem: $data[self::PROPERTY_CREATOR_SYSTEM] ?? null,
            rrnPreauth: $data[self::PROPERTY_RRN_PREAUTH] ?? null,
            preauthOperationId: $data[self::PROPERTY_PREAUTH_OPERATION_ID] ?? null,
            preauthCoinAmount: isset($data[self::PROPERTY_PREAUTH_COIN_AMOUNT])
                ? (int)$data[self::PROPERTY_PREAUTH_COIN_AMOUNT] : null,
            preauthEcomOperationId: $data[self::PROPERTY_PREAUTH_ECOM_OPERATION_ID] ?? null,
        );
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRrn(): string
    {
        return $this->rrn;
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
     * @return string
     */
    public function getEcomOperationId(): string
    {
        return $this->ecomOperationId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getTransactionType(): ?int
    {
        return $this->transactionType;
    }

    /**
     * @return string|null
     */
    public function getMerchantRequestId(): ?string
    {
        return $this->merchantRequestId;
    }

    /**
     * @return string
     */
    public function getTransactionCurrency(): string
    {
        return $this->transactionCurrency;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreationDateTime(): ?DateTimeImmutable
    {
        return $this->creationDateTime;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getModificationDateTime(): ?DateTimeImmutable
    {
        return $this->modificationDateTime;
    }

    /**
     * @return array
     */
    public function getTransactionResponseInfo(): array
    {
        return $this->transactionResponseInfo;
    }

    /**
     * @return string|null
     */
    public function getProductType(): ?string
    {
        return $this->productType;
    }

    /**
     * @return string|null
     */
    public function getNotificationUrl(): ?string
    {
        return $this->notificationUrl;
    }

    /**
     * @return string|null
     */
    public function getPaymentServiceType(): ?string
    {
        return $this->paymentServiceType;
    }

    /**
     * @return bool
     */
    public function isNotificationEncryption(): bool
    {
        return $this->notificationEncryption;
    }

    /**
     * @return bool
     */
    public function isNotificationSignature(): bool
    {
        return $this->notificationSignature;
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
    public function getProcessingTerminalId(): ?string
    {
        return $this->processingTerminalId;
    }

    /**
     * @return string|null
     */
    public function getProcessingMerchantId(): ?string
    {
        return $this->processingMerchantId;
    }

    /**
     * @return string|null
     */
    public function getCreatorSystem(): ?string
    {
        return $this->creatorSystem;
    }

    /**
     * @return string|null
     */
    public function getRrnPreauth(): ?string
    {
        return $this->rrnPreauth;
    }

    /**
     * @return string|null
     */
    public function getPreauthOperationId(): ?string
    {
        return $this->preauthOperationId;
    }

    /**
     * @return int|null
     */
    public function getPreauthCoinAmount(): ?int
    {
        return $this->preauthCoinAmount;
    }

    /**
     * @return string|null
     */
    public function getPreauthEcomOperationId(): ?string
    {
        return $this->preauthEcomOperationId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_TYPE => $this->type,
            self::PROPERTY_RRN => $this->rrn,
            self::PROPERTY_COIN_AMOUNT => $this->coinAmount,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_OPERATION_ID => $this->operationId,
            self::PROPERTY_ECOM_OPERATION_ID => $this->ecomOperationId,
            self::PROPERTY_STATUS => $this->status,
            self::PROPERTY_TRANSACTION_TYPE => $this->transactionType,
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_TRANSACTION_CURRENCY => $this->transactionCurrency,
            self::PROPERTY_CREATION_DATE_TIME =>
                $this->creationDateTime?->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT),
            self::PROPERTY_MODIFICATION_DATE_TIME =>
                $this->modificationDateTime?->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT),
            self::PROPERTY_TRANSACTION_RESPONSE_INFO => $this->transactionResponseInfo,
            self::PROPERTY_PRODUCT_TYPE => $this->productType,
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl,
            self::PROPERTY_PAYMENT_SERVICE_TYPE => $this->paymentServiceType,
            self::PROPERTY_NOTIFICATION_ENCRYPTION => $this->notificationEncryption,
            self::PROPERTY_NOTIFICATION_SIGNATURE => $this->notificationSignature,
            self::PROPERTY_HPP_ORDER_ID => $this->hppOrderId,
            self::PROPERTY_PROCESSING_TERMINAL_ID => $this->processingTerminalId,
            self::PROPERTY_PROCESSING_MERCHANT_ID => $this->processingMerchantId,
            self::PROPERTY_CREATOR_SYSTEM => $this->creatorSystem,
            self::PROPERTY_RRN_PREAUTH => $this->rrnPreauth,
            self::PROPERTY_PREAUTH_OPERATION_ID => $this->preauthOperationId,
            self::PROPERTY_PREAUTH_COIN_AMOUNT => $this->preauthCoinAmount,
            self::PROPERTY_PREAUTH_ECOM_OPERATION_ID => $this->preauthEcomOperationId,
        ];
    }
}
