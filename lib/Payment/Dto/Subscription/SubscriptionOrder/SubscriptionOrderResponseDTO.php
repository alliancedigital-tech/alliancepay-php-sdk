<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\SubscriptionOrder;

/**
 * Class SubscriptionOrderResponseDTO.
 *
 * Response of `subscription-orders/create_v1`. Only `hppOrderId`/`redirectUrl` are confirmed by the source
 * specification; no real captured response is available (see plan's open question №1) — other fields the
 * bank may echo back (e.g. `merchantRequestId`, `planId`) are not modeled and would need adding once confirmed.
 */
final class SubscriptionOrderResponseDTO
{
    private const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    private const PROPERTY_REDIRECT_URL = 'redirectUrl';

    public function __construct(
        private string $hppOrderId,
        private string $redirectUrl,
    ) {
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
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param array $data
     * @return self
     * @throws \RuntimeException when a required field is missing — the message embeds the raw response
     *     so the real bank payload shape can be inspected (see class docblock: this DTO is unconfirmed).
     */
    public static function fromArray(array $data): self
    {
        $hppOrderId = $data[self::PROPERTY_HPP_ORDER_ID] ?? throw new \RuntimeException(
            'subscription-orders/create_v1 response is missing "hppOrderId". Raw response: '
            . json_encode($data, JSON_UNESCAPED_UNICODE)
        );
        $redirectUrl = $data[self::PROPERTY_REDIRECT_URL] ?? throw new \RuntimeException(
            'subscription-orders/create_v1 response is missing "redirectUrl". Raw response: '
            . json_encode($data, JSON_UNESCAPED_UNICODE)
        );

        return new self(
            hppOrderId: $hppOrderId,
            redirectUrl: $redirectUrl,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_HPP_ORDER_ID => $this->hppOrderId,
            self::PROPERTY_REDIRECT_URL => $this->redirectUrl,
        ];
    }
}
