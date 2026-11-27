<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Exceptions;

/**
 * Class FiscalizationException.
 */
class FiscalizationException extends AlliancePayException
{
    public const MSG_CODE_MISSING_REQUIRED_FIELD = 'missing_required_field';
    public const MSG_CODE_INVALID_FORMAT = 'invalid_format';
    public const MSG_CODE_PAYMENT_NOT_FOUND = 'payment_not_found';
    public const MSG_CODE_ORIGINAL_RECEIPT_NOT_FOUND = 'original_receipt_not_found';

    /**
     * @return string|null Business error code from the API response (`msgCode`), if any.
     */
    public function getMsgCode(): ?string
    {
        $msgCode = $this->payload['msgCode'] ?? null;

        return is_string($msgCode) ? $msgCode : null;
    }
}
