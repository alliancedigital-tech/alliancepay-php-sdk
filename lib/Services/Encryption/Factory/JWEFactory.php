<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Encryption\Factory;

use SimpleJWT\JWE;

/**
 * Class JWEFactory.
 */
class JWEFactory
{
    public static function create(array $headers, string $payload): JWE
    {
        return new JWE($headers, $payload);
    }
}
