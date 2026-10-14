<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Services\Jws;

use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Services\Jws\JwsSigningService;
use PHPUnit\Framework\TestCase;
use SimpleJWT\JWT;
use SimpleJWT\Keys\KeyFactory;
use SimpleJWT\Keys\KeySet;

/**
 * Class JwsSigningServiceTest.
 */
class JwsSigningServiceTest extends TestCase
{
    private string $privateKeyPem;

    private string $publicKeyPem;

    protected function setUp(): void
    {
        $resource = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ]);
        $privateKeyPem = '';
        openssl_pkey_export($resource, $privateKeyPem);
        $this->privateKeyPem = $privateKeyPem;
        $this->publicKeyPem = openssl_pkey_get_details($resource)['key'];
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_sign_returns_verifiable_compact_jws(): void
    {
        $payload = ['amount' => 1000, 'currency' => 'UAH'];

        $token = JwsSigningService::sign($this->privateKeyPem, 'kid-1', '/ecom/jws/payments/hpp/create_order_v1', $payload);

        $this->assertCount(3, explode('.', $token));

        $key = KeyFactory::create($this->publicKeyPem, 'pem');
        $key->setKeyId('kid-1');
        $keySet = new KeySet();
        $keySet->add($key);

        $jwt = JWT::decode($token, $keySet, 'RS256');

        $this->assertSame($payload, $jwt->getClaims());
        $headers = $jwt->getHeaders();
        $this->assertSame('RS256', $headers['alg']);
        $this->assertSame('kid-1', $headers['kid']);
        $this->assertSame('/ecom/jws/payments/hpp/create_order_v1', $headers['targetUrl']);
        $this->assertArrayHasKey('ts', $headers);
        $this->assertArrayNotHasKey('typ', $headers);
        $this->assertArrayNotHasKey('iat', $jwt->getClaims());

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $headers['jti']
        );
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_sign_generates_unique_jti_per_call(): void
    {
        $first = JwsSigningService::sign($this->privateKeyPem, 'kid-1', '/x', []);
        $second = JwsSigningService::sign($this->privateKeyPem, 'kid-1', '/x', []);

        $decodeHeader = static function (string $token): array {
            return json_decode(base64_decode(strtr(explode('.', $token)[0], '-_', '+/')), true);
        };

        $this->assertNotSame($decodeHeader($first)['jti'], $decodeHeader($second)['jti']);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_sign_throws_when_private_key_is_empty(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('JWS Private Key (PEM) is not configured.');

        JwsSigningService::sign('', 'kid-1', '/x', []);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_sign_throws_when_kid_is_empty(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('JWS Key ID (kid) is not configured.');

        JwsSigningService::sign($this->privateKeyPem, '', '/x', []);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_sign_throws_when_key_is_not_rsa(): void
    {
        $resource = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        openssl_pkey_export($resource, $ecPrivateKeyPem);

        $this->expectException(EncryptionException::class);

        JwsSigningService::sign($ecPrivateKeyPem, 'kid-1', '/x', []);
    }
}
