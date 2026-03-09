<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Encryption;

use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Traits\ResponseHandlerTrait;
use AlliancePay\Sdk\Services\Encryption\Factory\JWEFactory;
use AlliancePay\Sdk\Services\Encryption\Factory\KeySetFactory;
use SimpleJWT\InvalidTokenException;
use SimpleJWT\JWE;
use SimpleJWT\Keys\KeyFactory;

/**
 * Class EncryptionService.
 */
class JweEncryptionService
{
    use ResponseHandlerTrait;
    private const ALGORITHM = 'ECDH-ES+A256KW';

    private const ENCRYPTION = 'A256GCM';

    private const HEADERS = [
        'alg' => self::ALGORITHM,
        'enc' => self::ENCRYPTION
    ];

    /**
     * @param array $data
     * @param array $publicServerKey
     * @return string
     * @throws EncryptionException
     */
    public static function encrypt(array $data, array $publicServerKey): string
    {
        $dataJson = json_encode($data);
        $key = KeyFactory::create(
            data: $publicServerKey,
            alg: self::ALGORITHM
        );
        $keySet = KeySetFactory::create();
        $keySet->add($key);
        $jwe = JweFactory::create(
            self::HEADERS,
            $dataJson
        );

        try {
            return $jwe->encrypt($keySet);
        } catch (InvalidTokenException $e) {
            throw new EncryptionException('Encryption failed: ' . $e->getMessage());
        }
    }

    /**
     * @param string $authentificationKey
     * @param string $jweToken
     * @return array
     * @throws EncryptionException
     */
    public static function decrypt(string $authentificationKey, string $jweToken): array
    {
        $decryptData = [];
        $key = KeyFactory::create(
            data: $authentificationKey, alg: self::ALGORITHM
        );
        $keySet = KeySetFactory::create();
        $keySet->add($key);

        try {
            $jweObj = JWE::decrypt(
                $jweToken,
                $keySet,
                self::ALGORITHM
            );
        } catch (InvalidTokenException $e) {
            throw new EncryptionException('Decryption failed: ' . $e->getMessage());
        }

        $decryptPlainText = $jweObj->getPlaintext();

        if ($decryptPlainText) {
            $decryptData = json_decode($decryptPlainText, true);
        }

        return $decryptData;
    }
}
