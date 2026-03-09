<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Services\Encryption;

use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Services\Encryption\JweEncryptionService;
use AlliancePay\Sdk\Exceptions\EncryptionException;

/**
 * Class JweEncryptionServiceTest.
 */
class JweEncryptionServiceTest extends TestCase
{
    /**
     * This is fake key randomly generated.
     *
     * @return array
     */
    private function getTestPublicKey(): array
    {
        return json_decode('{"kty": "EC","use": "enc","crv": "P-384",
        "x": "g4NSeivuFxFCkRo7mHgi6PA8_RFgO0obFZgT0ZufBT1hmvVF-4Zb9arnn7sbVHyT",
        "y": "8ufRfdLcyh2OmOE9m35iNskBHt7JI3xGpB-gLzDpgD0pnxVEql0RIC5nL6z_TXN_",
        "alg": "ECDH-ES+A256KW"}',
            true
        );
    }

    /**
     * This is fake key randomly generated.
     *
     * @return array
     */
    private function getTestSecondPublicKey(): array
    {
        return json_decode('{"kty": "EC","use": "enc","crv": "P-384",
        "x": "-IE1FSvy57GmU_d7y9F-wi4ID3dr61u_s1jTaW7OH8bguFZxdxksSo0rwe5oPfuL",
        "y": "LJiuXLUtfsxg43qSEzZ3-1HLCj8d-eOnCTfY0zo5Kzxrn3tj3jqPq3iH32I4_2WM",
        "alg": "ECDH-ES+A256KW"}',
            true
        );
    }

    /**
     * This is fake key randomly generated.
     *
     * @return string
     */
    private function getTestPrivateKey(): string
    {
        return '{"kty": "EC","d": "-eTnNxa0wuJq8fZNfMPXLZDybbdUyB8hLHVxLbsAK7HKAIRHxQC21f95i_bufNli",
        "use": "enc","crv": "P-384","x": "g4NSeivuFxFCkRo7mHgi6PA8_RFgO0obFZgT0ZufBT1hmvVF-4Zb9arnn7sbVHyT",
        "y": "8ufRfdLcyh2OmOE9m35iNskBHt7JI3xGpB-gLzDpgD0pnxVEql0RIC5nL6z_TXN_","alg": "ECDH-ES+A256KW"}';
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_it_can_encrypt_and_decrypt_data(): void
    {
        $publicKey = $this->getTestPublicKey();
        $privateKey = $this->getTestPrivateKey();

        $originalData = ['test' => 'payload', 'id' => 123];

        $token = JweEncryptionService::encrypt($originalData, $publicKey);
        $this->assertIsString($token);
        $this->assertCount(5, explode('.', $token));

        $decryptedData = JweEncryptionService::decrypt($privateKey, $token);
        $this->assertEquals($originalData, $decryptedData);
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_it_throws_exception_on_invalid_token(): void
    {
        $this->expectException(EncryptionException::class);
        JweEncryptionService::decrypt($this->getTestPrivateKey(), 'not.a.valid.jwe.token');
    }

    /**
     * @return void
     * @throws EncryptionException
     */
    public function test_it_fails_with_wrong_key(): void
    {
        $token = JweEncryptionService::encrypt(['foo' => 'bar'], $this->getTestSecondPublicKey());
        $this->expectException(EncryptionException::class);
        JweEncryptionService::decrypt($this->getTestPrivateKey(), $token);
    }
}
