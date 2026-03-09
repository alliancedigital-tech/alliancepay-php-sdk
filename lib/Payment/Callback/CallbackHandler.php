<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Callback;

use AlliancePay\Sdk\Exceptions\CallbackException;
use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Payment\Dto\Callback\CallbackDTO;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Encryption\JweEncryptionService;

/**
 * Class CallbackHandler.
 */
class CallbackHandler
{
    /**
     * @param AuthorizationDTO $authorizationDTO
     * @param array|null $payload
     * @return CallbackDTO
     * @throws CallbackException
     */
    public function handle( AuthorizationDTO $authorizationDTO, array $payload = null): CallbackDTO
    {
        if (empty($payload)) {
            $headers = array_change_key_case(getallheaders(), CASE_LOWER);
            $rawBody = file_get_contents('php://input');
            $isEncrypted = isset($headers['x-encrypted_body'])
                && $headers['x-encrypted_body'] === 'true';
            $isPlainText = isset($headers['content-type'])
                && str_contains($headers['content-type'], 'text/plain');

            if ($isEncrypted && $isPlainText) {
                try {
                    $rawBody = JweEncryptionService::decrypt(
                        $authorizationDTO->getAuthenticationKey(),
                        $rawBody
                    );
                } catch (EncryptionException $e) {
                    throw new CallbackException($e->getMessage());
                }
            }

            $payload = json_decode($rawBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new CallbackException("Invalid JSON payload: " . json_last_error_msg());
            }

            if (empty($payload)) {
                throw new CallbackException('Empty callback payload');
            }
        }

        return CallbackDTO::fromArray($payload);
    }
}
