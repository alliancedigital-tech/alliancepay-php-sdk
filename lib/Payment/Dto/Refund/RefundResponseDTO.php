<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Refund;

use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;
use Exception;

/**
 * Class RefundResponseDTO.
 */
final class RefundResponseDTO
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
    public const PROPERTY_NOTIFICATION_ENCRYPTION = 'notificationEncryption';
    public const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    public const PROPERTY_RRN_ORIGINAL = 'rrnOriginal';
    public const PROPERTY_ORIGINAL_OPERATION_ID = 'originalOperationId';
    public const PROPERTY_ORIGINAL_COIN_AMOUNT = 'originalCoinAmount';
    public const PROPERTY_ORIGINAL_ECOM_OPERATION_ID = 'originalEcomOperationId';
    public const PROPERTY_NOTIFICATION_SIGNATURE = 'notificationSignature';
    public const PROPERTY_PROCESSING_TERMINAL_ID = 'processingTerminalId';
    public const PROPERTY_PROCESSING_MERCHANT_ID = 'processingMerchantId';
    public const PROPERTY_CREATOR_SYSTEM = 'creatorSystem';
    public const PROPERTY_MERCHANT_NAME = 'merchantName';
    public const PROPERTY_APPROVAL_CODE = 'approvalCode';
    public const PROPERTY_MERCHANT_COMMISSION = 'merchantCommission';
    public const PROPERTY_BANK_CODE = 'bankCode';
    public const PROPERTY_PAYMENT_SYSTEM = 'paymentSystem';
    public const PROPERTY_PAYMENT_SERVICE_TYPE = 'paymentServiceType';
    public const PROPERTY_EXTERNAL_CARD_TOKEN = 'externalCardToken';

    public function __construct(
        private string $type,
        private string $rrn,
        private int $coinAmount,
        private string $merchantId,
        private string $operationId,
        private string $ecomOperationId,
        private string $status,
        private string $merchantRequestId,
        private string $transactionCurrency,
        private ?DateTimeImmutable $creationDateTime,
        private ?DateTimeImmutable $modificationDateTime,
        private array $transactionResponseInfo,
        private string $productType,
        private string $hppOrderId,
        private ?int $transactionType,
        private ?string $notificationUrl = '',
        private bool $notificationEncryption = false,
        private ?string $rrnOriginal = '',
        private ?string $originalOperationId = '',
        private ?int $originalCoinAmount = 0,
        private ?string $originalEcomOperationId = '',
        private bool $notificationSignature = false,
        private ?string $processingTerminalId = '',
        private ?string $processingMerchantId = '',
        private ?string $creatorSystem = '',
        private ?string $merchantName = '',
        private ?string $approvalCode = '',
        private ?float $merchantCommission = 0.0,
        private ?string $bankCode = '',
        private ?string $paymentSystem = '',
        private ?string $paymentServiceType = '',
        private ?string $externalCardToken = ''
    ) {}

    /**
     * @param array $refundData
     * @return self
     * @throws Exception
     */
    public static function createFromArray(array $refundData): self
    {
        return new self(
            type: $refundData[self::PROPERTY_TYPE],
            rrn: $refundData[self::PROPERTY_RRN],
            coinAmount: $refundData[self::PROPERTY_COIN_AMOUNT],
            merchantId: $refundData[self::PROPERTY_MERCHANT_ID],
            operationId: $refundData[self::PROPERTY_OPERATION_ID],
            ecomOperationId: $refundData[self::PROPERTY_ECOM_OPERATION_ID],
            status: $refundData[self::PROPERTY_STATUS],
            merchantRequestId: $refundData[self::PROPERTY_MERCHANT_REQUEST_ID],
            transactionCurrency: $refundData[self::PROPERTY_TRANSACTION_CURRENCY],
            creationDateTime: DateTimeImmutableProvider::fromString(
                $refundData[self::PROPERTY_CREATION_DATE_TIME],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            modificationDateTime: DateTimeImmutableProvider::fromString(
                $refundData[self::PROPERTY_MODIFICATION_DATE_TIME],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            transactionResponseInfo: $refundData[self::PROPERTY_TRANSACTION_RESPONSE_INFO],
            productType: $refundData[self::PROPERTY_PRODUCT_TYPE],
            hppOrderId: $refundData[self::PROPERTY_HPP_ORDER_ID],
            transactionType: $refundData[self::PROPERTY_TRANSACTION_TYPE],
            notificationUrl: $refundData[self::PROPERTY_NOTIFICATION_URL] ?? null,
            notificationEncryption: $refundData[self::PROPERTY_NOTIFICATION_ENCRYPTION],
            rrnOriginal: $refundData[self::PROPERTY_RRN],
            originalOperationId: $refundData[self::PROPERTY_ORIGINAL_OPERATION_ID],
            originalCoinAmount: $refundData[self::PROPERTY_ORIGINAL_COIN_AMOUNT],
            originalEcomOperationId: $refundData[self::PROPERTY_ORIGINAL_ECOM_OPERATION_ID],
            notificationSignature: $refundData[self::PROPERTY_NOTIFICATION_SIGNATURE],
            processingTerminalId: $refundData[self::PROPERTY_PROCESSING_TERMINAL_ID],
            processingMerchantId: $refundData[self::PROPERTY_PROCESSING_MERCHANT_ID],
            creatorSystem: $refundData[self::PROPERTY_CREATOR_SYSTEM],
            merchantName: $refundData[self::PROPERTY_MERCHANT_NAME] ?? null,
            approvalCode: $refundData[self::PROPERTY_APPROVAL_CODE] ?? null,
            merchantCommission: $refundData[self::PROPERTY_MERCHANT_COMMISSION] ?? null,
            bankCode: $refundData[self::PROPERTY_BANK_CODE] ?? null,
            paymentSystem: $refundData[self::PROPERTY_PAYMENT_SYSTEM] ?? null,
            paymentServiceType: $refundData[self::PROPERTY_PAYMENT_SERVICE_TYPE] ?? null,
            externalCardToken: $refundData[self::PROPERTY_EXTERNAL_CARD_TOKEN] ?? null,
        );
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
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_TRANSACTION_CURRENCY => $this->transactionCurrency,
            self::PROPERTY_CREATION_DATE_TIME =>
                $this->creationDateTime->format(DateTimeImmutableProvider::DEFAULT_TIMEZONE),
            self::PROPERTY_MODIFICATION_DATE_TIME =>
                $this->modificationDateTime->format(DateTimeImmutableProvider::DEFAULT_TIMEZONE),
            self::PROPERTY_TRANSACTION_RESPONSE_INFO => $this->transactionResponseInfo,
            self::PROPERTY_HPP_ORDER_ID => $this->hppOrderId,
            self::PROPERTY_TRANSACTION_TYPE => $this->transactionType,
            self::PROPERTY_PRODUCT_TYPE => $this->productType,
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl,
            self::PROPERTY_NOTIFICATION_ENCRYPTION => $this->notificationEncryption,
            self::PROPERTY_NOTIFICATION_SIGNATURE => $this->notificationSignature,
            self::PROPERTY_RRN_ORIGINAL => $this->rrnOriginal,
            self::PROPERTY_ORIGINAL_OPERATION_ID => $this->originalOperationId,
            self::PROPERTY_ORIGINAL_COIN_AMOUNT => $this->originalCoinAmount,
            self::PROPERTY_ORIGINAL_ECOM_OPERATION_ID => $this->originalEcomOperationId,
            self::PROPERTY_PROCESSING_TERMINAL_ID => $this->processingTerminalId,
            self::PROPERTY_PROCESSING_MERCHANT_ID => $this->processingMerchantId,
            self::PROPERTY_CREATOR_SYSTEM => $this->creatorSystem,
            self::PROPERTY_MERCHANT_NAME => $this->merchantName,
            self::PROPERTY_APPROVAL_CODE => $this->approvalCode,
            self::PROPERTY_MERCHANT_COMMISSION => $this->merchantCommission,
            self::PROPERTY_BANK_CODE => $this->bankCode,
            self::PROPERTY_PAYMENT_SYSTEM => $this->paymentSystem,
            self::PROPERTY_PAYMENT_SERVICE_TYPE => $this->paymentServiceType,
            self::PROPERTY_EXTERNAL_CARD_TOKEN => $this->externalCardToken
        ];
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
     * @return string
     */
    public function getMerchantRequestId(): string
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
     * @return DateTimeImmutable
     */
    public function getCreationDateTime(): DateTimeImmutable
    {
        return $this->creationDateTime;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getModificationDateTime(): DateTimeImmutable
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
     * @return string
     */
    public function getProductType(): string
    {
        return $this->productType;
    }

    /**
     * @return string
     */
    public function getHppOrderId(): string
    {
        return $this->hppOrderId;
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
    public function getNotificationUrl(): ?string
    {
        return $this->notificationUrl;
    }

    /**
     * @return bool
     */
    public function isNotificationEncryption(): bool
    {
        return $this->notificationEncryption;
    }

    /**
     * @return string|null
     */
    public function getRrnOriginal(): ?string
    {
        return $this->rrnOriginal;
    }

    /**
     * @return string|null
     */
    public function getOriginalOperationId(): ?string
    {
        return $this->originalOperationId;
    }

    /**
     * @return int|null
     */
    public function getOriginalCoinAmount(): ?int
    {
        return $this->originalCoinAmount;
    }

    /**
     * @return string|null
     */
    public function getOriginalEcomOperationId(): ?string
    {
        return $this->originalEcomOperationId;
    }

    /**
     * @return bool
     */
    public function isNotificationSignature(): bool
    {
        return $this->notificationSignature;
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
    public function getMerchantName(): ?string
    {
        return $this->merchantName;
    }

    /**
     * @return string|null
     */
    public function getApprovalCode(): ?string
    {
        return $this->approvalCode;
    }

    /**
     * @return float|null
     */
    public function getMerchantCommission(): ?float
    {
        return $this->merchantCommission;
    }

    /**
     * @return string|null
     */
    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    /**
     * @return string|null
     */
    public function getPaymentSystem(): ?string
    {
        return $this->paymentSystem;
    }

    /**
     * @return string|null
     */
    public function getPaymentServiceType(): ?string
    {
        return $this->paymentServiceType;
    }

    /**
     * @return string|null
     */
    public function getExternalCardToken(): ?string
    {
        return $this->externalCardToken;
    }
}
