<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Payment;

/**
 * Class RecurringPaymentTriggerRequestDTO.
 *
 * Manual/support-only trigger of `payments/recurring_subscription_v1`. Field set is confirmed by the
 * bank's own API doc (`Recurring payment docs/api_scheduled_payments-recurring_subscription_v1.md`):
 * `merchantRequestId`, `merchantId`, `date` and `scheduledPaymentId` are required; `merchantComment` is
 * optional. There is NO `hppOrderId` field in this endpoint's contract at all — an earlier revision of
 * this DTO carried one (copied from the cancel-subscription-order shape), but the bank's validator
 * simply ignored it as an unrecognised extra field rather than rejecting it, which masked the fact that
 * it was never actually used. Resolution of "which payment to trigger" is by `scheduledPaymentId` alone;
 * the consuming application must already track this locally (populated from prior callbacks) — the bank
 * has no "list pending scheduled payments" endpoint to look it up some other way.
 *
 * `date` is a full timestamp string with milliseconds and UTC offset, e.g. `2024-12-13 15:35:41.170+02:00`
 * (same non-ISO-8601 "space, not T" format observed in other bank responses) — NOT a bare `YYYY-MM-DD`.
 */
final class RecurringPaymentTriggerRequestDTO
{
    private const PROPERTY_MERCHANT_REQUEST_ID = 'merchantRequestId';
    private const PROPERTY_MERCHANT_ID = 'merchantId';
    private const PROPERTY_DATE = 'date';
    private const PROPERTY_SCHEDULED_PAYMENT_ID = 'scheduledPaymentId';
    private const PROPERTY_MERCHANT_COMMENT = 'merchantComment';

    public function __construct(
        private string $merchantRequestId,
        private string $merchantId,
        private string $date,
        private string $scheduledPaymentId,
        private ?string $merchantComment = null,
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
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getScheduledPaymentId(): string
    {
        return $this->scheduledPaymentId;
    }

    /**
     * @return string|null
     */
    public function getMerchantComment(): ?string
    {
        return $this->merchantComment;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            merchantRequestId: $data[self::PROPERTY_MERCHANT_REQUEST_ID],
            merchantId: $data[self::PROPERTY_MERCHANT_ID],
            date: $data[self::PROPERTY_DATE],
            scheduledPaymentId: $data[self::PROPERTY_SCHEDULED_PAYMENT_ID],
            merchantComment: $data[self::PROPERTY_MERCHANT_COMMENT] ?? null,
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
            self::PROPERTY_DATE => $this->date,
            self::PROPERTY_SCHEDULED_PAYMENT_ID => $this->scheduledPaymentId,
        ];

        if ($this->merchantComment !== null) {
            $data[self::PROPERTY_MERCHANT_COMMENT] = $this->merchantComment;
        }

        return $data;
    }
}
