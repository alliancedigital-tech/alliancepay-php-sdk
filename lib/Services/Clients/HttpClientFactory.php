<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Clients;

use Composer\InstalledVersions;
use GuzzleHttp\Client;

/**
 * Class ClientFactory.
 */
class HttpClientFactory
{
    /**
     * @return HttpClientInterface
     */
    public static function create(): HttpClientInterface
    {
        if (class_exists('\GuzzleHttp\Client')) {
            $version = self::getGuzzleVersion();

            if ($version && version_compare($version, '7.0.0', '>=')) {
                return new GuzzleAdapter();
            }
        }

        return new CurlAdapter();
    }

    /**
     * @return string|null
     */
    private static function getGuzzleVersion(): ?string
    {
        if (class_exists('\Composer\InstalledVersions')) {
            return InstalledVersions::getPrettyVersion('guzzlehttp/guzzle');
        }

        if (defined('\GuzzleHttp\Client::VERSION')) {
            return Client::VERSION;
        }

        return null;
    }
}
