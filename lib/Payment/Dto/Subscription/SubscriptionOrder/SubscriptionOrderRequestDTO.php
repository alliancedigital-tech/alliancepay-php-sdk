<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Subscription\SubscriptionOrder\Validation\ValidateSubscriptionOrderData;

/**
 * Class SubscriptionOrderRequestDTO.
 */
final class SubscriptionOrderRequestDTO
{
    private const PROPERTY_PLAN_ID = 'planId';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    private const PROPERTY_PURPOSE = 'purpose';
    private const PROPERTY_MERCHANT_COMMENT = 'merchantComment';
    private const PROPERTY_NOTIFICATION_URL = 'notificationUrl';
    private const PROPERTY_NOTIFICATION_ENCRYPTION = 'notificationEncryption';
    private const PROPERTY_STATUS_PAGE_TYPE = 'statusPageType';
    private const PROPERTY_SUCCESS_URL = 'successUrl';
    private const PROPERTY_FAIL_URL = 'failUrl';
    private const PROPERTY_HPP_PAYMENT_METHODS = 'hppPaymentMethods';
    private const PROPERTY_DIRECT_TYPE = 'directType';
    private const PROPERTY_CUSTOMER_DATA = 'customerData';

    public function __construct(
        private string $planId,
        private string $merchantId,
        private string $merchantRequestId,
        private string $statusPageType,
        private string $successUrl,
        private string $failUrl,
        private array $hppPaymentMethods,
        private array $customerData,
        private ?string $purpose = null,
        private ?string $merchantComment = null,
        private ?string $notificationUrl = null,
        private string|bool|null $notificationEncryption = null,
        private ?string $directType = null,
    ) {
    }

    /**
     * @return string
     */
    public function getPlanId(): string
    {
        return $this->planId;
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
    public function getMerchantRequestId(): string
    {
        return $this->merchantRequestId;
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
     * @return array
     */
    public function getHppPaymentMethods(): array
    {
        return $this->hppPaymentMethods;
    }

    /**
     * @return array
     */
    public function getCustomerData(): array
    {
        return $this->customerData;
    }

    /**
     * @return string|null
     */
    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    /**
     * @return string|null
     */
    public function getMerchantComment(): ?string
    {
        return $this->merchantComment;
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
    public function getDirectType(): ?string
    {
        return $this->directType;
    }

    /**
     * @param array $data
     * @return self
     * @throws ValidateDataException
     */
    public static function fromArray(array $data): self
    {
        $data = ValidateSubscriptionOrderData::validateSubscriptionOrderRequiredData($data);

        return new self(
            planId: $data[self::PROPERTY_PLAN_ID],
            merchantId: $data[self::PROPERTY_MERCHANT_ID],
            merchantRequestId: $data[self::PROPERTY_MERCHANT_REQUEST_ID],
            statusPageType: $data[self::PROPERTY_STATUS_PAGE_TYPE],
            successUrl: $data[self::PROPERTY_SUCCESS_URL],
            failUrl: $data[self::PROPERTY_FAIL_URL],
            hppPaymentMethods: $data[self::PROPERTY_HPP_PAYMENT_METHODS],
            customerData: $data[self::PROPERTY_CUSTOMER_DATA],
            purpose: $data[self::PROPERTY_PURPOSE] ?? null,
            merchantComment: $data[self::PROPERTY_MERCHANT_COMMENT] ?? null,
            notificationUrl: $data[self::PROPERTY_NOTIFICATION_URL] ?? null,
            notificationEncryption: $data[self::PROPERTY_NOTIFICATION_ENCRYPTION] ?? null,
            directType: $data[self::PROPERTY_DIRECT_TYPE] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            self::PROPERTY_PLAN_ID => $this->planId,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_STATUS_PAGE_TYPE => $this->statusPageType,
            self::PROPERTY_SUCCESS_URL => $this->successUrl,
            self::PROPERTY_FAIL_URL => $this->failUrl,
            self::PROPERTY_HPP_PAYMENT_METHODS => $this->hppPaymentMethods,
            self::PROPERTY_CUSTOMER_DATA => $this->customerData,
            self::PROPERTY_PURPOSE => $this->purpose,
            self::PROPERTY_MERCHANT_COMMENT => $this->merchantComment,
            self::PROPERTY_NOTIFICATION_URL => $this->notificationUrl,
            self::PROPERTY_NOTIFICATION_ENCRYPTION => $this->notificationEncryption,
            self::PROPERTY_DIRECT_TYPE => $this->directType,
        ];

        return ValidateSubscriptionOrderData::clearSubscriptionOrderData($data);
    }
}
