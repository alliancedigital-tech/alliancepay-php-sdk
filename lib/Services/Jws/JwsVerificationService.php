<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Jws;

use AlliancePay\Sdk\Exceptions\EncryptionException;
use SimpleJWT\JWT;
use SimpleJWT\Keys\ECKey;
use SimpleJWT\Keys\KeySet;
use Throwable;

/**
 * Class JwsVerificationService.
 */
class JwsVerificationService
{
    /**
     * @param string $jws
     * @return array
     * @throws EncryptionException
     */
    public static function deserialiseHeader(string $jws): array
    {
        try {
            $deserialised = JWT::deserialise($jws);
            $headers = $deserialised['signatures'][0]['headers'] ?? [];
        } catch (Throwable $e) {
            throw new EncryptionException('JWS parse error: ' . $e->getMessage());
        }

        if (empty($headers['kid']) || empty($headers['alg'])) {
            throw new EncryptionException('JWS is missing kid or alg in header.');
        }

        return ['kid' => $headers['kid'], 'alg' => $headers['alg']];
    }

    /**
     * @param string $jws
     * @param array $jwk
     * @param string $alg
     * @return array
     * @throws EncryptionException
     */
    public static function verify(string $jws, array $jwk, string $alg): array
    {
        try {
            $deserialised = JWT::deserialise($jws);
        } catch (Throwable $e) {
            throw new EncryptionException('JWS verification failed: ' . $e->getMessage());
        }

        // SimpleJWT\JWT::decode() passes the decoded payload straight into a constructor
        // typed `array $claims` without checking its shape first — a payload that decodes
        // to a JSON scalar (not an object) crashes with a vendor-internal TypeError instead
        // of a catchable, meaningful error. Check it ourselves before calling decode().
        if (!is_array($deserialised['claims'] ?? null)) {
            $claims = $deserialised['claims'] ?? null;
            // Signature isn't verified yet at this point (same as deserialiseHeader() above
            // peeking at kid/alg before verification) — include the raw value anyway, since
            // this is purely diagnostic (thrown as an error, never treated as trusted business
            // data) and is the only way to tell WHY the bank's payload isn't the expected
            // object without reproducing against a live/sandbox endpoint.
            $preview = is_scalar($claims) ? var_export($claims, true) : gettype($claims);
            throw new EncryptionException(
                'JWS verification failed: decoded payload is not a JSON object (got '
                . gettype($claims) . '): ' . substr((string) $preview, 0, 500)
            );
        }

        try {
            $key = new ECKey($jwk, 'php');
            if (!empty($jwk['kid'])) {
                $key->setKeyId($jwk['kid']);
            }

            $keySet = new KeySet();
            $keySet->add($key);

            return JWT::decode($jws, $keySet, $alg)->getClaims();
        } catch (Throwable $e) {
            throw new EncryptionException('JWS verification failed: ' . $e->getMessage());
        }
    }
}
