<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Order;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Order\Validation\ValidateOrderData;

/**
 * Class OrderRequestDTO.
 */
final class OrderRequestDTO
{
    private const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_HPP_PAY_TYPE = 'hppPayType';
    private const PROPERTY_COIN_AMOUNT = 'coinAmount';
    private const PROPERTY_PAYMENT_METHODS = 'paymentMethods';
    private const PROPERTY_SUCCESS_URL = 'successUrl';
    private const PROPERTY_FAIL_URL = 'failUrl';
    private const PROPERTY_STATUS_PAGE_TYPE = 'statusPageType';
    private const PROPERTY_CUSTOMER_DATA = 'customerData';
    private const PROPERTY_MERCHANT_COMMENT = 'merchantComment';
    private const PROPERTY_DIRECT_TYPE = 'directType';
    private const PROPERTY_HPP_TRY_MODE = 'hppTryMode';
    private const PROPERTY_EXPIRATION_TIME_MINUTES = 'expirationTimeMinutes';
    private const PROPERTY_LANGUAGE = 'language';
    private const PROPERTY_NOTIFICATION_URL = 'notificationUrl';
    private const PROPERTY_NOTIFICATION_ENCRYPTION = 'notificationEncryption';
    private const PROPERTY_PURPOSE = 'purpose';
    private const PROPERTY_PRIORITY_BANK_CODE = 'priorityBankCode';
    private const PROPERTY_PAYMENT_CATEGORY_GOAL = 'paymentCategoryGoal';
    private const PROPERTY_GENERATE_QR_NBU = 'generateQrNbu';

    public function __construct(
        private string $merchantRequestId,
        private string $merchantId,
        private string $hppPayType,
        private int $coinAmount,
        private array $paymentMethods,
        private string $successUrl,
        private string $failUrl,
        private string $statusPageType,
        private array $customerData,
        private ?string $merchantComment = null,
        private ?string $directType = null,
        private ?string $hppTryMode = null,
        private ?int $expirationTimeMinutes = null,
        private ?string $language = null,
        private ?string $notificationUrl = null,
        private string|bool|null $notificationEncryption = null,
        private ?string $purpose = null,
        private ?string $priorityBankCode = null,
        private ?string $paymentCategoryGoal = null,
        private bool $generateQrNbu = false,
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
    public function getHppPayType(): string
    {
        return $this->hppPayType;
    }

    /**
     * @return int
     */
    public function getCoinAmount(): int
    {
        return $this->coinAmount;
    }

    /**
     * @return array
     */
    public function getPaymentMethods(): array
    {
        return $this->paymentMethods;
    }

    /**
     * @return string
     */
    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    /**
     * @return string
     */
    public function getFailUrl(): string
    {
        return $this->failUrl;
    }

    /**
     * @return string
     */
    public function getStatusPageType(): string
    {
        return $this->statusPageType;
    }

    /**
     * @return string
     */
    public function getMerchantComment(): string
    {
        return $this->merchantComment;
    }

    /**
     * @return array
     */
    public function getCustomerData(): array
    {
        return $this->customerData;
    }

    /**
     * @param array $orderData
     * @return self
     * @throws ValidateDataException
     */
    public static function fromArray(array $orderData): self
    {
        $orderData = ValidateOrderData::validateOrderRequiredData($orderData);

        return new self(
            merchantRequestId: $orderData[self::PROPERTY_MERCHANT_REQUEST_ID],
            merchantId: $orderData[self::PROPERTY_MERCHANT_ID],
            hppPayType: $orderData[self::PROPERTY_HPP_PAY_TYPE],
            coinAmount: $orderData[self::PROPERTY_COIN_AMOUNT],
            paymentMethods: $orderData[self::PROPERTY_PAYMENT_METHODS],
            successUrl: $orderData[self::PROPERTY_SUCCESS_URL],
            failUrl: $orderData[self::PROPERTY_FAIL_URL],
            statusPageType: $orderData[self::PROPERTY_STATUS_PAGE_TYPE],
            customerData: $orderData[self::PROPERTY_CUSTOMER_DATA],
            merchantComment: $orderData[self::PROPERTY_MERCHANT_COMMENT] ?? null,
            directType: $orderData[self::PROPERTY_DIRECT_TYPE] ?? null,
            hppTryMode: $orderData[self::PROPERTY_HPP_TRY_MODE] ?? null,
            expirationTimeMinutes: isset($orderData[self::PROPERTY_EXPIRATION_TIME_MINUTES])
                ? (int)$orderData[self::PROPERTY_EXPIRATION_TIME_MINUTES] : null,
            language: $orderData[self::PROPERTY_LANGUAGE] ?? null,
            notificationUrl: $orderData[self::PROPERTY_NOTIFICATION_URL] ?? null,
            notificationEncryption: $orderData[self::PROPERTY_NOTIFICATION_ENCRYPTION] ?? null,
            purpose: $orderData[self::PROPERTY_PURPOSE] ?? null,
            priorityBankCode: $orderData[self::PROPERTY_PRIORITY_BANK_CODE] ?? null,
            paymentCategoryGoal: $orderData[self::PROPERTY_PAYMENT_CATEGORY_GOAL] ?? null,
            generateQrNbu: (bool)($orderData[self::PROPERTY_GENERATE_QR_NBU] ?? false),
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
            self::PROPERTY_HPP_PAY_TYPE => $this->hppPayType,
            self::PROPERTY_COIN_AMOUNT => $this->coinAmount,
            self::PROPERTY_PAYMENT_METHODS => $this->paymentMethods,
            self::PROPERTY_SUCCESS_URL => $this->successUrl,
            self::PROPERTY_FAIL_URL => $this->failUrl,
            self::PROPERTY_STATUS_PAGE_TYPE => $this->statusPageType,
            self::PROPERTY_CUSTOMER_DATA => $this->customerData,
            self::PROPERTY_DIRECT_TYPE => $this->directType,
            self::PROPERTY_HPP_TRY_MODE => $this->hppTryMode,
            self::PROPERTY_EXPIRATION_TIME_MINUTES => $this->expirationTimeMinutes,
            self::PROPERTY_LANGUAGE => $this->language,
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl,
            self::PROPERTY_NOTIFICATION_ENCRYPTION => $this->notificationEncryption,
            self::PROPERTY_PURPOSE => $this->purpose,
            self::PROPERTY_PRIORITY_BANK_CODE => $this->priorityBankCode,
            self::PROPERTY_PAYMENT_CATEGORY_GOAL => $this->paymentCategoryGoal,
            self::PROPERTY_GENERATE_QR_NBU => $this->generateQrNbu,
        ];

        return ValidateOrderData::clearOrderData($data);
    }
}
