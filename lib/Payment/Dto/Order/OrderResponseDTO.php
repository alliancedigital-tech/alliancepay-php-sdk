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
 * Class OrderResponseDTO.
 */
final class OrderResponseDTO
{
    private const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    private const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    private const PROPERTY_HPP_PAY_TYPE = 'hppPayType';
    private const PROPERTY_PAYMENT_METHODS = 'paymentMethods';
    private const PROPERTY_ORDER_STATUS = 'orderStatus';
    private const PROPERTY_COIN_AMOUNT = 'coinAmount';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_EXPIRED_ORDER_DATE = 'expiredOrderDate';
    private const PROPERTY_REDIRECT_URL = 'redirectUrl';
    private const PROPERTY_CREATE_DATE  = 'createDate';
    private const PROPERTY_STATUS_URL = 'statusUrl';
    private const PROPERTY_NBU_QR_CODE = 'nbuQrCode';

    public function __construct(
        private string $hppOrderId,
        private string $merchantRequestId,
        private string $hppPayType,
        private array $paymentMethods,
        private string $orderStatus,
        private int $coinAmount,
        private string $merchantId,
        private ?DateTimeImmutable $expiredOrderDate,
        private string $redirectUrl,
        private ?DateTimeImmutable $createDate,
        private string $statusUrl,
        private string $nbuQrCode
    ) {

    }

    /**
     * @param array $data
     * @return self
     * @throws Exception
     */
    public static function fromArray(array $data): self
    {
        return new self(
            hppOrderId: $data[self::PROPERTY_HPP_ORDER_ID],
            merchantRequestId: $data[self::PROPERTY_MERCHANT_REQUEST_ID],
            hppPayType: $data[self::PROPERTY_HPP_PAY_TYPE],
            paymentMethods: $data[self::PROPERTY_PAYMENT_METHODS],
            orderStatus: $data[self::PROPERTY_ORDER_STATUS],
            coinAmount: $data[self::PROPERTY_COIN_AMOUNT],
            merchantId: $data[self::PROPERTY_MERCHANT_ID],
            expiredOrderDate: DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_EXPIRED_ORDER_DATE],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            redirectUrl: $data[self::PROPERTY_REDIRECT_URL],
            createDate: DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_CREATE_DATE],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            statusUrl: $data[self::PROPERTY_STATUS_URL],
            nbuQrCode: $data[self::PROPERTY_NBU_QR_CODE] ?? ''
        );
    }

    /**
     * @return string
     */
    public function getHppOrderId(): string
    {
        return $this->hppOrderId;
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
    public function getHppPayType(): string
    {
        return $this->hppPayType;
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
    public function getOrderStatus(): string
    {
        return $this->orderStatus;
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
     * @return DateTimeImmutable|null
     */
    public function getExpiredOrderDate(): ?DateTimeImmutable
    {
        return $this->expiredOrderDate;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreateDate(): ?DateTimeImmutable
    {
        return $this->createDate;
    }

    /**
     * @return string
     */
    public function getStatusUrl(): string
    {
        return $this->statusUrl;
    }

    /**
     * @return string
     */
    public function getNbuQrCode(): string
    {
        return $this->nbuQrCode;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_HPP_ORDER_ID => $this->hppOrderId,
            self::PROPERTY_MERCHANT_REQUEST_ID => $this->merchantRequestId,
            self::PROPERTY_HPP_PAY_TYPE => $this->hppPayType,
            self::PROPERTY_PAYMENT_METHODS => $this->paymentMethods,
            self::PROPERTY_ORDER_STATUS => $this->orderStatus,
            self::PROPERTY_COIN_AMOUNT => $this->coinAmount,
            self::PROPERTY_MERCHANT_ID => $this->merchantId,
            self::PROPERTY_EXPIRED_ORDER_DATE =>
                $this->expiredOrderDate->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT),
            self::PROPERTY_REDIRECT_URL => $this->redirectUrl,
            self::PROPERTY_CREATE_DATE =>
                $this->createDate->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT),
            self::PROPERTY_STATUS_URL => $this->statusUrl,
            self::PROPERTY_NBU_QR_CODE => $this->nbuQrCode
        ];
    }
}
