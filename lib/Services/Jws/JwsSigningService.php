<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Jws;

use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use SimpleJWT\JWT;
use SimpleJWT\Keys\KeyFactory;
use SimpleJWT\Keys\KeySet;
use Throwable;

/**
 * Class JwsSigningService.
 */
class JwsSigningService
{
    private const ALGORITHM = 'RS256';

    /**
     * @param string $privateKeyPem
     * @param string $kid
     * @param string $targetUrl
     * @param array $payload
     * @return string
     * @throws EncryptionException
     */
    public static function sign(string $privateKeyPem, string $kid, string $targetUrl, array $payload): string
    {
        if (empty($privateKeyPem)) {
            throw new EncryptionException('JWS Private Key (PEM) is not configured.');
        }

        if (empty($kid)) {
            throw new EncryptionException('JWS Key ID (kid) is not configured.');
        }

        $headers = [
            'alg' => self::ALGORITHM,
            'ts' => (string)(int)(microtime(true) * 1000),
            'targetUrl' => $targetUrl,
            'kid' => $kid,
            'jti' => GenerateRequestIdentification::generateRequestId(),
        ];

        try {
            $key = KeyFactory::create($privateKeyPem, 'pem');
            $key->setKeyId($kid);

            $keySet = new KeySet();
            $keySet->add($key);

            return (new JWT($headers, $payload))->encode($keySet, kid: null, auto_complete: false);
        } catch (Throwable $e) {
            throw new EncryptionException('JWS signing failed: ' . $e->getMessage());
        }
    }
}
