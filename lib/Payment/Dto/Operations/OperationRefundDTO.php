<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Operations;

use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;
use Exception;

/**
 * Class OperationRefundDTO.
 */
class OperationRefundDTO
{
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_RRN = 'rrn';
    public const PROPERTY_COIN_AMOUNT = 'coinAmount';
    public const PROPERTY_MERCHANT_ID = 'merchantId';
    public const PROPERTY_OPERATION_ID = 'operationId';
    public const PROPERTY_ECOM_OPERATION_ID = 'ecomOperationId';
    public const PROPERTY_MERCHANT_NAME = 'merchantName';
    public const PROPERTY_APPROVAL_CODE = 'approvalCode';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_TRANSACTION_TYPE = 'transactionType';
    public const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    public const PROPERTY_TRANSACTION_CURRENCY = 'transactionCurrency';
    public const PROPERTY_CREATION_DATE_TIME = 'creationDateTime';
    public const PROPERTY_MODIFICATION_DATE_TIME = 'modificationDateTime';
    public const PROPERTY_PROCESSING_DATE_TIME = 'processingDateTime';
    public const PROPERTY_TRANSACTION_RESPONSE_INFO = 'transactionResponseInfo';
    public const PROPERTY_BANK_CODE = 'bankCode';
    public const PROPERTY_PAYMENT_SYSTEM = 'paymentSystem';
    public const PROPERTY_PRODUCT_TYPE = 'productType';
    public const PROPERTY_NOTIFICATION_ENCRYPTION = 'notificationEncryption';
    public const PROPERTY_NOTIFICATION_SIGNATURE = 'notificationSignature';
    public const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    public const PROPERTY_PROCESSING_TERMINAL_ID = 'processingTerminalId';
    public const PROPERTY_PROCESSING_MERCHANT_ID = 'processingMerchantId';
    public const PROPERTY_CREATOR_SYSTEM = 'creatorSystem';
    public const PROPERTY_RRN_ORIGINAL = 'rrnOriginal';
    public const PROPERTY_ORIGINAL_OPERATION_ID = 'originalOperationId';
    public const PROPERTY_ORIGINAL_COIN_AMOUNT = 'originalCoinAmount';
    public const PROPERTY_CARD_NUMBER_MASK = 'cardNumberMask';
    public const PROPERTY_ORIGINAL_ECOM_OPERATION_ID = 'originalEcomOperationId';
    public const OPERATION_TYPE = 'REFUND';

    public function __construct(
        private string $type,
        private string $rrn,
        private int $coinAmount,
        private string $merchantId,
        private string $operationId,
        private string $ecomOperationId,
        private ?string $merchantName,
        private ?string $approvalCode,
        private string $status,
        private ?int $transactionType,
        private string $merchantRequestId,
        private string $transactionCurrency,
        private ?DateTimeImmutable $creationDateTime,
        private ?DateTimeImmutable $modificationDateTime,
        private ?DateTimeImmutable $processingDateTime,
        private array $transactionResponseInfo,
        private ?string $bankCode,
        private ?string $paymentSystem,
        private string $productType,
        private bool $notificationEncryption,
        private bool $notificationSignature,
        private string $hppOrderId,
        private ?string $processingTerminalId,
        private ?string $processingMerchantId,
        private ?string $creatorSystem,
        private ?string $rrnOriginal,
        private ?string $originalOperationId,
        private ?string $originalCoinAmount,
        private ?string $cardNumberMask,
        private ?string $originalEcomOperationId,
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
            merchantName: $data[self::PROPERTY_MERCHANT_NAME] ?? null,
            approvalCode: $data[self::PROPERTY_APPROVAL_CODE] ?? null,
            status: $data[self::PROPERTY_STATUS] ?? '',
            transactionType: isset($data[self::PROPERTY_TRANSACTION_TYPE])
                ? (int)$data[self::PROPERTY_TRANSACTION_TYPE] : null,
            merchantRequestId: $data[self::PROPERTY_MERCHANT_REQUEST_ID] ?? '',
            transactionCurrency: $data[self::PROPERTY_TRANSACTION_CURRENCY] ?? '',
            creationDateTime: DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_CREATION_DATE_TIME],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            modificationDateTime: DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_MODIFICATION_DATE_TIME],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            processingDateTime: DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_PROCESSING_DATE_TIME],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            transactionResponseInfo: $data[self::PROPERTY_TRANSACTION_RESPONSE_INFO] ?? [],
            bankCode: $data[self::PROPERTY_BANK_CODE] ?? null,
            paymentSystem: $data[self::PROPERTY_PAYMENT_SYSTEM] ?? null,
            productType: $data[self::PROPERTY_PRODUCT_TYPE] ?? '',
            notificationEncryption: (bool)($data[self::PROPERTY_NOTIFICATION_ENCRYPTION] ?? false),
            notificationSignature: $data[self::PROPERTY_NOTIFICATION_SIGNATURE] ?? null,
            hppOrderId: $data[self::PROPERTY_HPP_ORDER_ID] ?? '',
            processingTerminalId: $data[self::PROPERTY_PROCESSING_TERMINAL_ID] ?? null,
            processingMerchantId: $data[self::PROPERTY_PROCESSING_MERCHANT_ID] ?? null,
            creatorSystem: $data[self::PROPERTY_CREATOR_SYSTEM] ?? null,
            rrnOriginal: $data[self::PROPERTY_RRN_ORIGINAL] ?? null,
            originalOperationId: $data[self::PROPERTY_ORIGINAL_OPERATION_ID] ?? null,
            originalCoinAmount: isset($data[self::PROPERTY_ORIGINAL_COIN_AMOUNT])
                ? (string)$data[self::PROPERTY_ORIGINAL_COIN_AMOUNT] : null,
            cardNumberMask: $data[self::PROPERTY_CARD_NUMBER_MASK] ?? null,
            originalEcomOperationId: $data[self::PROPERTY_ORIGINAL_ECOM_OPERATION_ID] ?? null
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
     * @return DateTimeImmutable|null
     */
    public function getProcessingDateTime(): ?DateTimeImmutable
    {
        return $this->processingDateTime;
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
     * @return string
     */
    public function getProductType(): string
    {
        return $this->productType;
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
     * @return string|null
     */
    public function getOriginalCoinAmount(): ?string
    {
        return $this->originalCoinAmount;
    }

    /**
     * @return string|null
     */
    public function getCardNumberMask(): ?string
    {
        return $this->cardNumberMask;
    }

    /**
     * @return string|null
     */
    public function getOriginalEcomOperationId(): ?string
    {
        return $this->originalEcomOperationId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = get_object_vars($this);

        foreach ($data as $key => $value) {
            if ($value instanceof DateTimeImmutable) {
                $data[$key] = $value->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT);
            }
        }

        return $data;
    }
}
