<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Operations;

use AlliancePay\Sdk\Exceptions\CallbackException;

/**
 * Class OperationFactory.
 */
final class OperationFactory
{
    /**
     * @param array $operation
     * @return OperationPurchaseDTO|OperationRefundDTO
     * @throws CallbackException
     */
    public static function fromArray(array $operation): OperationPurchaseDTO|OperationRefundDTO
    {
        $type = $operation[OperationPurchaseDTO::PROPERTY_TYPE]
            ?? throw new CallbackException('Operation type is missing');

        return match ($type) {
            OperationPurchaseDTO::OPERATION_TYPE,
            OperationPurchaseDTO::OPERATION_TYPE_A2A,
            OperationPurchaseDTO::OPERATION_TYPE_PREAUTH,
            OperationPurchaseDTO::OPERATION_TYPE_COMPLETION => OperationPurchaseDTO::fromArray($operation),
            OperationRefundDTO::OPERATION_TYPE              => OperationRefundDTO::fromArray($operation),
            default => throw new CallbackException('Unknown operation type: ' . $type),
        };
    }
}
