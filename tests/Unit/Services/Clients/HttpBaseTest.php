<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Services\Clients;

use AlliancePay\Sdk\Exceptions\AuthenticationException;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Services\Clients\HttpBase\HttpBase;
use AlliancePay\Sdk\Services\Clients\HttpClientInterface;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;
use SimpleJWT\JWT;
use SimpleJWT\Keys\ECKey;
use SimpleJWT\Keys\KeyFactory;
use SimpleJWT\Keys\KeySet;

class HttpBaseTest extends TestCase
{
    private HttpBase $httpBase;

    private string $rsaPrivateKeyPem;

    private string $rsaPublicKeyPem;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->httpBase = new HttpBase();

        $resource = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ]);
        $privateKeyPem = '';
        openssl_pkey_export($resource, $privateKeyPem);
        $this->rsaPrivateKeyPem = $privateKeyPem;
        $this->rsaPublicKeyPem = openssl_pkey_get_details($resource)['key'];
    }

    /**
     * @return AuthorizationDTO
     * @throws AuthenticationException
     */
    protected function getAuthDtoObject(): AuthorizationDTO
    {
        return AuthorizationDTO::fromArray(
            [
                'baseUrl' => 'http://localhost',
                'merchantId' => 'merchantId',
                'jwsPrivateKey' => $this->rsaPrivateKeyPem,
                'jwsKeyId' => 'test-kid',
            ]
        );
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_it_builds_correct_options_from_auth_dto(): void
    {
        $authDto = $this->getAuthDtoObject();

        $data = ['amount' => 1000, 'currency' => 'UAH'];

        $options = $this->httpBase->buildOptionsFromAuthDto($authDto, HttpClientInterface::PATH_CREATE_ORDER, $data);

        $this->assertArrayHasKey('headers', $options);
        $headers = $options['headers'];

        $this->assertEquals('V1', $headers['x-api_version']);
        $this->assertEquals('application/jws', $headers['Content-Type']);
        $this->assertEquals('application/json', $headers['Accept']);

        $this->assertArrayHasKey('body', $options);
        $this->assertCount(3, explode('.', $options['body']));

        $key = KeyFactory::create($this->rsaPublicKeyPem, 'pem');
        $key->setKeyId('test-kid');
        $keySet = new KeySet();
        $keySet->add($key);

        $jwt = JWT::decode($options['body'], $keySet, 'RS256');
        $this->assertSame($data, $jwt->getClaims());
        $this->assertSame(HttpClientInterface::PATH_CREATE_ORDER, $jwt->getHeaders()['targetUrl']);
        $this->assertArrayNotHasKey('typ', $jwt->getHeaders());
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_it_for_fails_without_required_params(): void
    {
        $this->expectException(AuthenticationException::class);
        AuthorizationDTO::fromArray(
            [
                'baseUrl' => 'http://localhost',
                'merchantId' => 'merchantId'
            ]
        );
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_it_still_sends_jws_content_type_and_api_version_when_content_type_omitted(): void
    {
        $authDto = $this->getAuthDtoObject();

        $options = $this->httpBase->buildOptionsFromAuthDto($authDto, HttpClientInterface::PATH_CREATE_ORDER, []);

        $this->assertSame('application/jws', $options['headers']['Content-Type']);
        $this->assertSame('V1', $options['headers']['x-api_version']);
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_it_sends_jwt_content_type_without_api_version_header(): void
    {
        $authDto = $this->getAuthDtoObject();

        $options = $this->httpBase->buildOptionsFromAuthDto(
            $authDto,
            HttpClientInterface::PATH_CREATE_PLAN,
            [],
            HttpClientInterface::CONTENT_TYPE_JWT
        );

        $this->assertSame('application/jwt', $options['headers']['Content-Type']);
        $this->assertArrayNotHasKey('x-api_version', $options['headers']);
    }

    /**
     * @return void
     */
    public function test_subscription_endpoint_url_getters_return_correct_endpoints(): void
    {
        $baseUrl = 'https://api.bankalliance.ua';

        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/plan/create_v1',
            $this->httpBase->getCreatePlanUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/plan/update_v1',
            $this->httpBase->getUpdatePlanUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/plan/get_v1',
            $this->httpBase->getGetPlanUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/plan/search_v1',
            $this->httpBase->getSearchPlansUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/plan/status_update_v1',
            $this->httpBase->getUpdatePlanStatusUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/plan/version/update_v1',
            $this->httpBase->getUpdatePlanVersionUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/plan/delete_v1',
            $this->httpBase->getDeletePlanVersionUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/version/delete_v1',
            $this->httpBase->getDeleteVersionUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/subscription-orders/create_v1',
            $this->httpBase->getCreateSubscriptionOrderUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/subscription-orders/cancel_v1',
            $this->httpBase->getCancelSubscriptionOrderUrl($baseUrl)
        );
        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/scheduled_payments/payments/recurring_subscription_v1',
            $this->httpBase->getRecurringSubscriptionPaymentUrl($baseUrl)
        );
    }

    /**
     * @return void
     */
    public function test_get_completion_url_returns_correct_endpoint(): void
    {
        $baseUrl = 'https://api.bankalliance.ua';

        $url = $this->httpBase->getCompletionUrl($baseUrl);

        $this->assertSame(
            'https://api.bankalliance.ua/ecom/jws/payments/preauth/completion_v1',
            $url
        );
    }

    /**
     * @return void
     */
    public function test_get_completion_url_with_trailing_slash(): void
    {
        $url = $this->httpBase->getCompletionUrl('https://api.example.com');

        $this->assertStringContainsString('/ecom/jws/payments/preauth/completion_v1', $url);
        $this->assertStringStartsWith('https://api.example.com', $url);
    }

    /**
     * @return void
     */
    public function test_parse_response_body_returns_plain_json_when_not_a_jws_token(): void
    {
        $adapter = $this->createResponseParsingAdapter([]);

        $result = $adapter->parseResponseBodyPublic(
            json_encode(['msgType' => 'ERROR', 'msgCode' => 'E1']),
            'https://api.test.ua/ecom/jws/payments/refund_v1'
        );

        $this->assertSame(['msgType' => 'ERROR', 'msgCode' => 'E1'], $result);
        $this->assertSame([], $adapter->requestedUrls);
    }

    /**
     * @return void
     */
    public function test_parse_response_body_verifies_jws_response_via_fetched_bank_public_key(): void
    {
        $kid = 'test-bank-kid-' . uniqid();
        $resource = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        $ec = openssl_pkey_get_details($resource)['ec'];
        $b64url = static fn (string $bin): string => rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');

        $privateJwk = ['kty' => 'EC', 'crv' => 'P-256', 'x' => $b64url($ec['x']), 'y' => $b64url($ec['y']), 'd' => $b64url($ec['d'])];
        $publicJwk = ['kty' => 'EC', 'crv' => 'P-256', 'x' => $b64url($ec['x']), 'y' => $b64url($ec['y']), 'kid' => $kid];

        $signingKey = new ECKey($privateJwk, 'php');
        $signingKey->setKeyId($kid);
        $signingKeySet = new KeySet();
        $signingKeySet->add($signingKey);

        $token = (new JWT(['alg' => 'ES256', 'kid' => $kid], ['hppOrderId' => 'ALB-1']))
            ->encode($signingKeySet, kid: null, auto_complete: false);

        $adapter = $this->createResponseParsingAdapter($publicJwk);

        $result = $adapter->parseResponseBodyPublic($token, 'https://api.test.ua/ecom/jws/payments/hpp/create_order_v1');

        $this->assertSame(['hppOrderId' => 'ALB-1'], $result);
        $this->assertCount(1, $adapter->requestedUrls);
        $this->assertSame('https://api.test.ua/ecom/keys/public_key/get_v1', $adapter->requestedUrls[0]);
    }

    /**
     * Regression: a plain JSON body that happens to contain exactly two literal dots (e.g.
     * two decimal fields, as subscription operations commonly do with coinAmount/
     * conversionRate) must never be misdetected as a compact JWS token.
     *
     * @return void
     */
    public function test_parse_response_body_does_not_misdetect_plain_json_with_two_dots_as_jws(): void
    {
        $adapter = $this->createResponseParsingAdapter([]);

        $body = json_encode([
            'hppOrderId' => 'ALB-1',
            'operation' => ['coinAmount' => 100.55, 'conversionRate' => 1.25],
        ]);
        $this->assertSame(2, substr_count($body, '.'), 'fixture must contain exactly two dots to exercise the bug');

        $result = $adapter->parseResponseBodyPublic($body, 'https://api.test.ua/ecom/jws/scheduled_payments/payments/recurring_subscription_v1');

        $this->assertSame(['hppOrderId' => 'ALB-1', 'operation' => ['coinAmount' => 100.55, 'conversionRate' => 1.25]], $result);
        $this->assertSame([], $adapter->requestedUrls);
    }

    /**
     * @param array $publicKeyResponseBody
     * @return object
     */
    private function createResponseParsingAdapter(array $publicKeyResponseBody): object
    {
        return new class ($publicKeyResponseBody) extends HttpBase implements HttpClientInterface {
            public array $requestedUrls = [];

            public function __construct(private array $publicKeyResponseBody)
            {
            }

            public function request(string $method, string $url, array $options = []): array
            {
                $this->requestedUrls[] = $url;

                return ['code' => 200, 'body' => $this->publicKeyResponseBody, 'headers' => []];
            }

            public function applyRequestId(string $requestId): void
            {
            }

            public function parseResponseBodyPublic(string $rawBody, string $requestUrl): ?array
            {
                return $this->parseResponseBody($rawBody, $requestUrl);
            }
        };
    }
}
