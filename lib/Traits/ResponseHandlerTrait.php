<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Traits;

use AlliancePay\Sdk\Exceptions\AlliancePayException;
use Exception;

/**
 * Trait ResponseHandlerTrait.
 */
trait ResponseHandlerTrait
{
    /**
     * @param array $result
     * @param string $exceptionClass
     * @throws Exception
     */
    public function handleResponseErrors(array $result, string $exceptionClass = AlliancePayException::class): void
    {
        $body = $result['body'] ?? null;

        if (!isset($body['msgType'])) {
            return;
        }

        $msgType = $body['msgType'];

        $baseInfo = sprintf(
            '[%s] Code: %s, RequestID: %s',
            $msgType,
            $body['msgCode'] ?? 'unknown',
            $body['requestId'] ?? 'none'
        );

        $errorMessage = '';

        if ($msgType === 'ERROR') {
            $message = $body['msgText'] ?? 'No message provided';
            $errorMessage = 'Operation failed: ' . $baseInfo . '. Message: ' . $message;
        } elseif ($msgType === 'VALIDATION_ERROR') {
            $errors = json_encode($body['validation']['errors'] ?? [], JSON_UNESCAPED_UNICODE);
            $errorMessage = 'Validation failed: ' . $baseInfo . '. Details: ' . $errors;
        }

        if ($errorMessage !== '') {
            throw new $exceptionClass($errorMessage, $body);
        }
    }
}
