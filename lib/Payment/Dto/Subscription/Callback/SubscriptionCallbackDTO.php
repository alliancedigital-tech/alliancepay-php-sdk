<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Subscription\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationFactory;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;
use AlliancePay\Sdk\Payment\Dto\Operations\OperationRefundDTO;

/**
 * Class SubscriptionCallbackDTO.
 *
 * Kept separate from {@see \AlliancePay\Sdk\Payment\Dto\Callback\CallbackDTO} rather than extending it:
 * that DTO requires `operation` and throws if it is missing, but subscription callbacks may legitimately
 * omit `operation` (e.g. the initial callback carrying only the future `scheduledPayments` schedule).
 * Weakening that invariant on the one-shot DTO would be wrong for its own, always-populated payloads.
 *
 * Resolution is strictly by `hppOrderId` — never by plan/product id, since one plan can have many active
 * subscriptions. Idempotency (by operation id) and safe merging of partial deliveries are left to the
 * consuming application, since this SDK has no persistence layer; `getRawPayload()` exists specifically
 * to make that merge possible (it lets a caller distinguish "field absent in this delivery" from
 * "field present and null").
 */
final class SubscriptionCallbackDTO
{
    private const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    private const PROPERTY_HPP_PAY_TYPE = 'hppPayType';
    private const PROPERTY_ORDER_STATUS = 'orderStatus';
    private const PROPERTY_RECURRING_TRANSACTION_TYPE = 'recurringTransactionType';
    private const PROPERTY_SCHEDULED_PAYMENT_ID = 'scheduledPaymentId';
    private const PROPERTY_SCHEDULED_PAYMENTS = 'scheduledPayments';
    private const PROPERTY_ECOM_ORDER_ID = 'ecomOrderId';
    private const PROPERTY_ECOM_OPERATION_ID = 'ecomOperationId';
    private const PROPERTY_OPERATION = 'operation';
    private const PROPERTY_OPERATIONS = 'operations';

    /**
     * @param ScheduledPaymentDTO[] $scheduledPayments
     */
    public function __construct(
        private string $hppOrderId,
        private ?string $hppPayType,
        private string $orderStatus,
        private ?string $recurringTransactionType,
        private ?string $scheduledPaymentId,
        private array $scheduledPayments,
        private ?string $ecomOrderId,
        private ?string $ecomOperationId,
        private OperationPurchaseDTO|OperationRefundDTO|null $operation,
        private array $rawPayload,
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
     * @return string|null
     */
    public function getHppPayType(): ?string
    {
        return $this->hppPayType;
    }

    /**
     * @return string
     */
    public function getOrderStatus(): string
    {
        return $this->orderStatus;
    }

    /**
     * @return string|null
     */
    public function getRecurringTransactionType(): ?string
    {
        return $this->recurringTransactionType;
    }

    /**
     * True when this delivery is the customer's first (initial) subscription payment rather than a
     * recurring one — the field is absent or empty on the initial delivery, per the source specification.
     *
     * @return bool
     */
    public function isInitialPayment(): bool
    {
        return $this->recurringTransactionType === null || $this->recurringTransactionType === '';
    }

    /**
     * @return string|null
     */
    public function getScheduledPaymentId(): ?string
    {
        return $this->scheduledPaymentId;
    }

    /**
     * @return ScheduledPaymentDTO[]
     */
    public function getScheduledPayments(): array
    {
        return $this->scheduledPayments;
    }

    /**
     * @return string|null
     */
    public function getEcomOrderId(): ?string
    {
        return $this->ecomOrderId;
    }

    /**
     * @return string|null
     */
    public function getEcomOperationId(): ?string
    {
        return $this->ecomOperationId;
    }

    /**
     * @return OperationPurchaseDTO|OperationRefundDTO|null
     */
    public function getOperation(): OperationPurchaseDTO|OperationRefundDTO|null
    {
        return $this->operation;
    }

    /**
     * The untouched decoded payload this DTO was built from — use it to tell "this delivery didn't mention
     * a field" (key absent) apart from "this delivery explicitly set it to null" (key present, null) when
     * merging successive partial callback deliveries for the same subscription.
     *
     * @return array
     */
    public function getRawPayload(): array
    {
        return $this->rawPayload;
    }

    /**
     * Best-effort amount reconciliation against an expected charge amount. Never throws and never signals
     * failure for a payment the bank has already processed — a mismatch is only ever a warning to surface
     * for manual review, per the source specification (section 4.3).
     *
     * @param int $expectedCoinAmount
     * @return string|null null when it matches (or nothing to compare yet), a warning message otherwise
     */
    public function reconcileAmount(int $expectedCoinAmount): ?string
    {
        if ($this->operation === null) {
            return null;
        }

        $actualCoinAmount = $this->operation->getCoinAmount();

        if ($actualCoinAmount === $expectedCoinAmount) {
            return null;
        }

        return sprintf(
            'Amount mismatch for hppOrderId=%s: expected %d, got %d',
            $this->hppOrderId,
            $expectedCoinAmount,
            $actualCoinAmount
        );
    }

    /**
     * @param array $data
     * @return self
     * @throws CallbackException
     */
    public static function fromArray(array $data): self
    {
        $hppOrderId = $data[self::PROPERTY_HPP_ORDER_ID]
            ?? throw new CallbackException('hppOrderId is missing');

        return new self(
            hppOrderId: $hppOrderId,
            hppPayType: $data[self::PROPERTY_HPP_PAY_TYPE] ?? null,
            orderStatus: $data[self::PROPERTY_ORDER_STATUS] ?? '',
            recurringTransactionType: $data[self::PROPERTY_RECURRING_TRANSACTION_TYPE] ?? null,
            scheduledPaymentId: isset($data[self::PROPERTY_SCHEDULED_PAYMENT_ID])
                ? (string)$data[self::PROPERTY_SCHEDULED_PAYMENT_ID] : null,
            scheduledPayments: array_map(
                static fn (array $payment): ScheduledPaymentDTO => ScheduledPaymentDTO::fromArray($payment),
                $data[self::PROPERTY_SCHEDULED_PAYMENTS] ?? []
            ),
            ecomOrderId: $data[self::PROPERTY_ECOM_ORDER_ID] ?? null,
            ecomOperationId: $data[self::PROPERTY_ECOM_OPERATION_ID] ?? null,
            operation: self::resolveOperation($data),
            rawPayload: $data,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_HPP_ORDER_ID => $this->hppOrderId,
            self::PROPERTY_HPP_PAY_TYPE => $this->hppPayType,
            self::PROPERTY_ORDER_STATUS => $this->orderStatus,
            self::PROPERTY_RECURRING_TRANSACTION_TYPE => $this->recurringTransactionType,
            self::PROPERTY_SCHEDULED_PAYMENT_ID => $this->scheduledPaymentId,
            self::PROPERTY_SCHEDULED_PAYMENTS => array_map(
                static fn (ScheduledPaymentDTO $payment): array => $payment->toArray(),
                $this->scheduledPayments
            ),
            self::PROPERTY_ECOM_ORDER_ID => $this->ecomOrderId,
            self::PROPERTY_ECOM_OPERATION_ID => $this->ecomOperationId,
            self::PROPERTY_OPERATION => $this->operation?->toArray(),
        ];
    }

    /**
     * `operation` is not always present in a subscription callback delivery; when operation details are
     * fetched separately the bank may also return them as an `operations` array instead of a single
     * `operation` object, depending on transaction count — this resolves either shape without throwing.
     *
     * @param array $data
     * @return OperationPurchaseDTO|OperationRefundDTO|null
     */
    private static function resolveOperation(array $data): OperationPurchaseDTO|OperationRefundDTO|null
    {
        if (isset($data[self::PROPERTY_OPERATION]) && is_array($data[self::PROPERTY_OPERATION])) {
            return OperationFactory::fromArray($data[self::PROPERTY_OPERATION]);
        }

        if (isset($data[self::PROPERTY_OPERATIONS]) && is_array($data[self::PROPERTY_OPERATIONS])) {
            $first = reset($data[self::PROPERTY_OPERATIONS]);
            if (is_array($first)) {
                return OperationFactory::fromArray($first);
            }
        }

        return null;
    }
}
