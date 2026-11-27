<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Fiscalization;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Fiscalization\Validation\ValidateFiscalizationData;

/**
 * Class FiscalizationRequestDTO.
 *
 * Idempotent on the Ecom side by `operationId`: re-sending returns the existing fiscalization.
 */
final class FiscalizationRequestDTO
{
    public const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    public const PROPERTY_MERCHANT_ID = 'merchantId';
    public const PROPERTY_OPERATION_ID = 'operationId';
    public const PROPERTY_RECEIPT_COMMENT = 'receiptComment';
    public const PROPERTY_NOTIFICATION_URL = 'notificationUrl';
    public const PROPERTY_PRODUCTS = 'products';

    /**
     * @param string $merchantRequestId
     * @param string $merchantId
     * @param string $operationId
     * @param string $notificationUrl Absolute callback URL (required).
     * @param FiscalizationProductDTO[] $products
     * @param string|null $receiptComment
     * @throws ValidateDataException
     */
    public function __construct(
        private string $merchantRequestId,
        private string $merchantId,
        private string $operationId,
        private string $notificationUrl,
        private array $products,
        private ?string $receiptComment = null
    ) {
        ValidateFiscalizationData::validateRequestData($this->toRawArray());
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
     * @return string
     */
    public function getNotificationUrl(): string
    {
        return $this->notificationUrl;
    }

    /**
     * @return FiscalizationProductDTO[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @return string|null
     */
    public function getReceiptComment(): ?string
    {
        return $this->receiptComment;
    }

    /**
     * @param array $data
     * @return self
     * @throws ValidateDataException
     */
    public static function fromArray(array $data): self
    {
        ValidateFiscalizationData::validateRequestData($data);

        $products = array_map(
            static fn (array $product): FiscalizationProductDTO => FiscalizationProductDTO::fromArray($product),
            array_values($data[self::PROPERTY_PRODUCTS])
        );

        $comment = $data[self::PROPERTY_RECEIPT_COMMENT] ?? null;

        return new self(
            merchantRequestId: (string)$data[self::PROPERTY_MERCHANT_REQUEST_ID],
            merchantId: (string)$data[self::PROPERTY_MERCHANT_ID],
            operationId: (string)$data[self::PROPERTY_OPERATION_ID],
            notificationUrl: (string)$data[self::PROPERTY_NOTIFICATION_URL],
            products: $products,
            receiptComment: ($comment === null || $comment === '') ? null : (string)$comment
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_OPERATION_ID => $this->operationId,
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl,
            self::PROPERTY_PRODUCTS => array_map(
                static fn (FiscalizationProductDTO $product): array => $product->toArray(),
                $this->products
            ),
        ];

        if ($this->receiptComment !== null && $this->receiptComment !== '') {
            $data[self::PROPERTY_RECEIPT_COMMENT] = $this->receiptComment;
        }

        return $data;
    }

    /**
     * @return array
     */
    private function toRawArray(): array
    {
        return [
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_OPERATION_ID => $this->operationId,
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl,
            self::PROPERTY_PRODUCTS => array_map(
                static fn (mixed $product): mixed => $product instanceof FiscalizationProductDTO
                    ? $product->toArray()
                    : $product,
                $this->products
            ),
        ];
    }
}
