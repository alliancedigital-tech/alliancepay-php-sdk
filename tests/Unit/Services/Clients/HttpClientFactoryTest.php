<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Services\Clients;

use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Services\Clients\HttpClientFactory;
use AlliancePay\Sdk\Services\Clients\GuzzleAdapter;
use AlliancePay\Sdk\Services\Clients\CurlAdapter;

/**
 * Class HttpClientFactoryTest.
 */
class HttpClientFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function test_it_creates_correct_adapter(): void
    {
        $client = HttpClientFactory::create();

        if (class_exists('\GuzzleHttp\Client')) {
            $this->assertInstanceOf(GuzzleAdapter::class, $client);
        } else {
            $this->assertInstanceOf(CurlAdapter::class, $client);
        }
    }
}
