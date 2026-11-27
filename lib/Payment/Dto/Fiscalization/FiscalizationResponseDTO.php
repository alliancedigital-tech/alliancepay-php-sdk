<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Fiscalization;

use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;
use Throwable;

/**
 * Class FiscalizationResponseDTO.
 */
final class FiscalizationResponseDTO
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_SUCCESS = 'SUCCESS';
    public const STATUS_FAIL = 'FAIL';

    public const OPERATION_TYPE_PURCHASE = 'PURCHASE';
    public const OPERATION_TYPE_REFUND = 'REFUND';

    public const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    public const PROPERTY_MERCHANT_ID = 'merchantId';
    public const PROPERTY_OPERATION_ID = 'operationId';
    public const PROPERTY_OPERATION_TYPE = 'operationType';
    public const PROPERTY_BUCKET_ID = 'bucketId';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_RECEIPT_COMMENT = 'receiptComment';
    public const PROPERTY_PRODUCTS = 'products';
    public const PROPERTY_FISCAL_NUMBER = 'fiscalNumber';
    public const PROPERTY_RECEIPT_URL = 'receiptUrl';
    public const PROPERTY_FISCALIZATION_DATE_TIME = 'fiscalizationDateTime';
    public const PROPERTY_FISCALIZATION_MODIFICATION_DATE_TIME = 'fiscalizationModificationDateTime';
    public const PROPERTY_ERROR_CODE = 'errorCode';
    public const PROPERTY_ERROR_DESCRIPTION = 'errorDescription';
    public const PROPERTY_CREATION_DATE_TIME = 'creationDateTime';
    public const PROPERTY_MODIFICATION_DATE_TIME = 'modificationDateTime';

    public function __construct(
        private string $merchantRequestId,
        private string $merchantId,
        private string $operationId,
        private ?string $operationType,
        private ?string $bucketId,
        private string $status,
        private ?string $receiptComment,
        private array $products,
        private ?string $fiscalNumber,
        private ?string $receiptUrl,
        private ?DateTimeImmutable $fiscalizationDateTime,
        private ?DateTimeImmutable $fiscalizationModificationDateTime,
        private ?string $errorCode,
        private ?string $errorDescription,
        private ?DateTimeImmutable $creationDateTime,
        private ?DateTimeImmutable $modificationDateTime
    ) {
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            merchantRequestId: (string)($data[self::PROPERTY_MERCHANT_REQUEST_ID] ?? ''),
            merchantId: (string)($data[self::PROPERTY_MERCHANT_ID] ?? ''),
            operationId: (string)($data[self::PROPERTY_OPERATION_ID] ?? ''),
            operationType: self::nullableString($data[self::PROPERTY_OPERATION_TYPE] ?? null),
            bucketId: self::nullableString($data[self::PROPERTY_BUCKET_ID] ?? null),
            status: strtoupper((string)($data[self::PROPERTY_STATUS] ?? '')),
            receiptComment: self::nullableString($data[self::PROPERTY_RECEIPT_COMMENT] ?? null),
            products: is_array($data[self::PROPERTY_PRODUCTS] ?? null) ? $data[self::PROPERTY_PRODUCTS] : [],
            fiscalNumber: self::nullableString($data[self::PROPERTY_FISCAL_NUMBER] ?? null),
            receiptUrl: self::nullableString($data[self::PROPERTY_RECEIPT_URL] ?? null),
            fiscalizationDateTime: self::parseDate($data[self::PROPERTY_FISCALIZATION_DATE_TIME] ?? null),
            fiscalizationModificationDateTime: self::parseDate(
                $data[self::PROPERTY_FISCALIZATION_MODIFICATION_DATE_TIME] ?? null
            ),
            errorCode: self::nullableString($data[self::PROPERTY_ERROR_CODE] ?? null),
            errorDescription: self::nullableString($data[self::PROPERTY_ERROR_DESCRIPTION] ?? null),
            creationDateTime: self::parseDate($data[self::PROPERTY_CREATION_DATE_TIME] ?? null),
            modificationDateTime: self::parseDate($data[self::PROPERTY_MODIFICATION_DATE_TIME] ?? null)
        );
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
     * @return string|null
     */
    public function getOperationType(): ?string
    {
        return $this->operationType;
    }

    /**
     * @return string|null
     */
    public function getBucketId(): ?string
    {
        return $this->bucketId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getReceiptComment(): ?string
    {
        return $this->receiptComment;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @return string|null
     */
    public function getFiscalNumber(): ?string
    {
        return $this->fiscalNumber;
    }

    /**
     * @return string|null
     */
    public function getReceiptUrl(): ?string
    {
        return $this->receiptUrl;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getFiscalizationDateTime(): ?DateTimeImmutable
    {
        return $this->fiscalizationDateTime;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getFiscalizationModificationDateTime(): ?DateTimeImmutable
    {
        return $this->fiscalizationModificationDateTime;
    }

    /**
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * @return string|null
     */
    public function getErrorDescription(): ?string
    {
        return $this->errorDescription;
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
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAIL;
    }

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->isSuccess() || $this->isFailed();
    }

    /**
     * @param DateTimeImmutable|null $storedModificationDateTime
     * @return bool
     */
    public function isNewerThan(?DateTimeImmutable $storedModificationDateTime): bool
    {
        if ($storedModificationDateTime === null || $this->modificationDateTime === null) {
            return true;
        }

        return $this->modificationDateTime >= $storedModificationDateTime;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $format = static fn (?DateTimeImmutable $date): ?string => $date?->format(DATE_ATOM);

        return [
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_OPERATION_ID => $this->operationId,
            self::PROPERTY_OPERATION_TYPE => $this->operationType,
            self::PROPERTY_BUCKET_ID => $this->bucketId,
            self::PROPERTY_STATUS => $this->status,
            self::PROPERTY_RECEIPT_COMMENT => $this->receiptComment,
            self::PROPERTY_PRODUCTS => $this->products,
            self::PROPERTY_FISCAL_NUMBER => $this->fiscalNumber,
            self::PROPERTY_RECEIPT_URL => $this->receiptUrl,
            self::PROPERTY_FISCALIZATION_DATE_TIME => $format($this->fiscalizationDateTime),
            self::PROPERTY_FISCALIZATION_MODIFICATION_DATE_TIME => $format($this->fiscalizationModificationDateTime),
            self::PROPERTY_ERROR_CODE => $this->errorCode,
            self::PROPERTY_ERROR_DESCRIPTION => $this->errorDescription,
            self::PROPERTY_CREATION_DATE_TIME => $format($this->creationDateTime),
            self::PROPERTY_MODIFICATION_DATE_TIME => $format($this->modificationDateTime),
        ];
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    private static function nullableString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string)$value;
    }

    /**
     * @param mixed $value
     * @return DateTimeImmutable|null
     */
    private static function parseDate(mixed $value): ?DateTimeImmutable
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return DateTimeImmutableProvider::fromString($value, DateTimeImmutableProvider::DEFAULT_TIMEZONE);
        } catch (Throwable) {
            return null;
        }
    }
}
