<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Encryption\Factory;

use SimpleJWT\Keys\KeySet;

/**
 * Class KeySetFactory.
 */
class KeySetFactory
{
    public static function create(): KeySet
    {
        return new KeySet();
    }
}
