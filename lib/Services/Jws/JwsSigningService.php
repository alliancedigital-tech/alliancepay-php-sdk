<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Jws;

use AlliancePay\Sdk\Exceptions\EncryptionException;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use SimpleJWT\JWT;
use SimpleJWT\Keys\ECKey;
use SimpleJWT\Keys\KeyFactory;
use SimpleJWT\Keys\KeyInterface;
use SimpleJWT\Keys\KeySet;
use SimpleJWT\Keys\RSAKey;
use Throwable;

/**
 * Class JwsSigningService.
 */
class JwsSigningService
{
    public const ALGORITHM_RS256 = 'RS256';
    public const ALGORITHM_ES256 = 'ES256';
    private const EC_CURVE_P256 = 'P-256';

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

        try {
            $key = KeyFactory::create($privateKeyPem, 'pem');
            $key->setKeyId($kid);

            $headers = [
                'alg' => self::resolveAlgorithm($key),
                'ts' => (string)(int)(microtime(true) * 1000),
                'targetUrl' => $targetUrl,
                'kid' => $kid,
                'jti' => GenerateRequestIdentification::generateRequestId(),
            ];

            $keySet = new KeySet();
            $keySet->add($key);

            return (new JWT($headers, $payload))->encode($keySet, kid: null, auto_complete: false);
        } catch (EncryptionException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new EncryptionException('JWS signing failed: ' . $e->getMessage());
        }
    }

    /**
     * @param KeyInterface $key
     * @return string
     * @throws EncryptionException
     */
    private static function resolveAlgorithm(KeyInterface $key): string
    {
        if ($key instanceof ECKey) {
            if ($key->getCurve() !== self::EC_CURVE_P256) {
                throw new EncryptionException(
                    sprintf(
                        'Unsupported EC curve "%s": the private key must use curve P-256 (prime256v1) for ES256.',
                        $key->getCurve()
                    )
                );
            }

            return self::ALGORITHM_ES256;
        }

        if ($key instanceof RSAKey) {
            return self::ALGORITHM_RS256;
        }

        throw new EncryptionException('Unsupported private key type: expected EC P-256 (ES256) or RSA (RS256).');
    }
}
