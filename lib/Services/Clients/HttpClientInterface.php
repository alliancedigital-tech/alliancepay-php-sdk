<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Clients;

use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;

/**
 * HttpClientInterface.
 */
interface HttpClientInterface
{
    public const METHOD_POST = 'POST';

    public const CONTENT_TYPE_JWS = 'application/jws';
    public const CONTENT_TYPE_JWT = 'application/jwt';
    public const CONTENT_TYPE_JSON = 'application/json';

    public const PATH_CREATE_ORDER = '/ecom/jws/payments/hpp/create_order_v1';
    public const PATH_OPERATIONS = '/ecom/jws/payments/hpp/operations_v1';
    public const PATH_REFUND = '/ecom/jws/payments/refund_v1';
    public const PATH_COMPLETION = '/ecom/jws/payments/preauth/completion_v1';
    public const PATH_PUBLIC_KEY = '/ecom/keys/public_key/get_v1';

    public const PATH_CREATE_PLAN = '/ecom/jws/scheduled_payments/plan/create_v1';
    public const PATH_UPDATE_PLAN = '/ecom/jws/scheduled_payments/plan/update_v1';
    public const PATH_GET_PLAN = '/ecom/jws/scheduled_payments/plan/get_v1';
    public const PATH_SEARCH_PLANS = '/ecom/jws/scheduled_payments/plan/search_v1';
    public const PATH_UPDATE_PLAN_STATUS = '/ecom/jws/scheduled_payments/plan/status_update_v1';
    public const PATH_UPDATE_PLAN_VERSION = '/ecom/jws/scheduled_payments/plan/version/update_v1';
    public const PATH_DELETE_PLAN_VERSION = '/ecom/jws/scheduled_payments/plan/delete_v1';
    public const PATH_DELETE_VERSION = '/ecom/jws/scheduled_payments/version/delete_v1';
    public const PATH_CREATE_SUBSCRIPTION_ORDER = '/ecom/jws/scheduled_payments/subscription-orders/create_v1';
    public const PATH_CANCEL_SUBSCRIPTION_ORDER = '/ecom/jws/scheduled_payments/subscription-orders/cancel_v1';
    public const PATH_RECURRING_SUBSCRIPTION_PAYMENT = '/ecom/jws/scheduled_payments/payments/recurring_subscription_v1';

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return array
     */
    public function request(string $method, string $url, array $options = []): array;

    /**
     * @param string $requestId
     * @return void
     */
    public function applyRequestId(string $requestId): void;

    /**
     * @param AuthorizationDTO $authorizationDto
     * @param string $targetUrl Endpoint path the JWS token will be signed for, e.g. self::PATH_CREATE_ORDER
     * @param array $data
     * @return array
     */
    public function buildOptionsFromAuthDto(
        AuthorizationDTO $authorizationDto,
        string $targetUrl,
        array $data,
        string $contentType = self::CONTENT_TYPE_JWS
    ): array;

    public function getCreateOrderUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getRefundUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getOperationsUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCompletionUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getPublicKeyUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCreatePlanUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getUpdatePlanUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getGetPlanUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getSearchPlansUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getUpdatePlanStatusUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getUpdatePlanVersionUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getDeletePlanVersionUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getDeleteVersionUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCreateSubscriptionOrderUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getCancelSubscriptionOrderUrl(string $baseUrl): string;

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getRecurringSubscriptionPaymentUrl(string $baseUrl): string;
}
