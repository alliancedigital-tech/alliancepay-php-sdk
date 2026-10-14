<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Clients\HttpBase;

use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Jws\BankPublicKeyProvider;
use AlliancePay\Sdk\Services\Jws\JwsSigningService;
use AlliancePay\Sdk\Services\Jws\JwsVerificationService;

/**
 * Class HttpBase.
 */
class HttpBase
{
    private const X_API_VERSION = 'V1';

    /**
     * @param AuthorizationDTO $authorizationDto
     * @param string $targetUrl
     * @param array $data
     * @param string $contentType
     * @return array
     */
    public function buildOptionsFromAuthDto(
        AuthorizationDTO $authorizationDto,
        string $targetUrl,
        array $data,
        string $contentType = HttpClientInterface::CONTENT_TYPE_JWS
    ): array {
        $jws = JwsSigningService::sign(
            $authorizationDto->getJwsPrivateKey(),
            $authorizationDto->getJwsKeyId(),
            $targetUrl,
            $data
        );

        $headers = [
            'x-request_id' => $this->requestId ?? uniqid(),
            'Content-Type' => $contentType,
            'Accept' => HttpClientInterface::CONTENT_TYPE_JSON,
        ];

        if ($contentType === HttpClientInterface::CONTENT_TYPE_JWS) {
            $headers['x-api_version'] = self::X_API_VERSION;
        }

        return [
            'headers' => $headers,
            'body' => $jws,
        ];
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCreateOrderUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_CREATE_ORDER;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getRefundUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_REFUND;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getOperationsUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_OPERATIONS;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCompletionUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_COMPLETION;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getPublicKeyUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_PUBLIC_KEY;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCreatePlanUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_CREATE_PLAN;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getUpdatePlanUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_UPDATE_PLAN;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getGetPlanUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_GET_PLAN;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getSearchPlansUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_SEARCH_PLANS;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getUpdatePlanStatusUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_UPDATE_PLAN_STATUS;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getUpdatePlanVersionUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_UPDATE_PLAN_VERSION;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getDeletePlanVersionUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_DELETE_PLAN_VERSION;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getDeleteVersionUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_DELETE_VERSION;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCreateSubscriptionOrderUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_CREATE_SUBSCRIPTION_ORDER;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCancelSubscriptionOrderUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_CANCEL_SUBSCRIPTION_ORDER;
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getRecurringSubscriptionPaymentUrl(string $baseUrl): string
    {
        return $baseUrl . HttpClientInterface::PATH_RECURRING_SUBSCRIPTION_PAYMENT;
    }

    /**
     * @param string $rawBody
     * @param string $requestUrl
     * @return array|null
     */
    protected function parseResponseBody(string $rawBody, string $requestUrl): ?array
    {
        $trimmed = trim($rawBody);

        // Try plain JSON FIRST (same precedence as SimpleJWT\Helper::detect() internally) —
        // a compact JWS never successfully json_decode()s, so this never misclassifies a real
        // token. Checking the "looks like a token" heuristic before this would misroute a
        // plain JSON response into JWS verification whenever it happens to contain exactly two
        // literal dots (e.g. two decimal fields such as coinAmount/conversionRate).
        $decoded = json_decode($rawBody, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        $isToken = $trimmed !== '' && !str_contains($trimmed, ' ') && substr_count($trimmed, '.') === 2;

        if ($isToken) {
            return $this->verifyJwsResponse($trimmed, $requestUrl);
        }

        return null;
    }

    /**
     * @param string $jws
     * @param string $requestUrl
     * @return array
     * @throws EncryptionException
     */
    private function verifyJwsResponse(string $jws, string $requestUrl): array
    {
        $header = JwsVerificationService::deserialiseHeader($jws);
        $publicKeyJwk = BankPublicKeyProvider::fetch($this, $this->extractBaseUrl($requestUrl), $header['kid']);

        return JwsVerificationService::verify($jws, $publicKeyJwk, $header['alg']);
    }

    /**
     * @param string $url
     * @return string
     */
    private function extractBaseUrl(string $url): string
    {
        $parsed = parse_url($url);
        $baseUrl = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');

        if (isset($parsed['port'])) {
            $baseUrl .= ':' . $parsed['port'];
        }

        return $baseUrl;
    }
}
