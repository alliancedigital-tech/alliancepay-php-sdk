<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Services\Jws;

use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Services\Jws\JwsVerificationService;
use PHPUnit\Framework\TestCase;
use SimpleJWT\JWT;
use SimpleJWT\Keys\ECKey;
use SimpleJWT\Keys\KeySet;

/**
 * Class JwsVerificationServiceTest.
 */
class JwsVerificationServiceTest extends TestCase
{
    private array $privateJwk;

    private array $publicJwk;

    private string $kid;

    protected function setUp(): void
    {
        $this->kid = 'bank-kid-' . uniqid();

        $resource = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        $ec = openssl_pkey_get_details($resource)['ec'];
        $b64url = static fn (string $bin): string => rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');

        $this->privateJwk = [
            'kty' => 'EC',
            'crv' => 'P-256',
            'x' => $b64url($ec['x']),
            'y' => $b64url($ec['y']),
            'd' => $b64url($ec['d']),
        ];
        $this->publicJwk = [
            'kty' => 'EC',
            'crv' => 'P-256',
            'x' => $b64url($ec['x']),
            'y' => $b64url($ec['y']),
            'kid' => $this->kid,
        ];
    }

    /**
     * @return string
     */
    private function signToken(array $claims): string
    {
        $key = new ECKey($this->privateJwk, 'php');
        $key->setKeyId($this->kid);
        $keySet = new KeySet();
        $keySet->add($key);

        return (new JWT(['alg' => 'ES256', 'kid' => $this->kid], $claims))
            ->encode($keySet, kid: null, auto_complete: false);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_deserialise_header_returns_kid_and_alg(): void
    {
        $token = $this->signToken(['hppOrderId' => 'ALB-1']);

        $header = JwsVerificationService::deserialiseHeader($token);

        $this->assertSame($this->kid, $header['kid']);
        $this->assertSame('ES256', $header['alg']);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_deserialise_header_throws_when_kid_is_missing(): void
    {
        $key = new ECKey($this->privateJwk, 'php');
        $keySet = new KeySet();
        $keySet->add($key);
        $token = (new JWT(['alg' => 'ES256'], ['a' => 1]))->encode($keySet, kid: null, auto_complete: false);

        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('JWS is missing kid or alg in header.');

        JwsVerificationService::deserialiseHeader($token);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_verify_returns_claims_for_valid_signature(): void
    {
        $claims = ['hppOrderId' => 'ALB-1', 'status' => 'SUCCESS'];
        $token = $this->signToken($claims);

        $result = JwsVerificationService::verify($token, $this->publicJwk, 'ES256');

        $this->assertSame($claims, $result);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_verify_throws_on_tampered_signature(): void
    {
        $token = $this->signToken(['a' => 1]);
        $tampered = substr($token, 0, -5) . 'AAAAA';

        $this->expectException(EncryptionException::class);

        JwsVerificationService::verify($tampered, $this->publicJwk, 'ES256');
    }

    /**
     * Regression: a token whose payload decodes to a JSON scalar (not an object) must fail
     * with a clear EncryptionException, not a vendor-internal TypeError leaking out of
     * SimpleJWT\JWT::decode()'s unchecked `array $claims` constructor parameter.
     *
     * @return void
     * @throws EncryptionException
     */
    public function test_verify_throws_clear_error_when_payload_is_not_a_json_object(): void
    {
        $key = new ECKey($this->privateJwk, 'php');
        $key->setKeyId($this->kid);
        $keySet = new KeySet();
        $keySet->add($key);

        $header = ['alg' => 'ES256', 'kid' => $this->kid];
        $b64url = static fn (string $bin): string => rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
        $signingInput = $b64url(json_encode($header)) . '.' . $b64url(json_encode('just-a-string'));

        $signer = \SimpleJWT\Crypt\AlgorithmFactory::create('ES256');
        $signature = $signer->sign($signingInput, $keySet, $this->kid);
        $token = $signingInput . '.' . $signature;

        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('decoded payload is not a JSON object (got string)');

        JwsVerificationService::verify($token, $this->publicJwk, 'ES256');
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_verify_throws_when_public_key_does_not_match(): void
    {
        $token = $this->signToken(['a' => 1]);

        $otherResource = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        $otherEc = openssl_pkey_get_details($otherResource)['ec'];
        $b64url = static fn (string $bin): string => rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
        $wrongPublicJwk = [
            'kty' => 'EC',
            'crv' => 'P-256',
            'x' => $b64url($otherEc['x']),
            'y' => $b64url($otherEc['y']),
            'kid' => $this->kid,
        ];

        $this->expectException(EncryptionException::class);

        JwsVerificationService::verify($token, $wrongPublicJwk, 'ES256');
    }
}
