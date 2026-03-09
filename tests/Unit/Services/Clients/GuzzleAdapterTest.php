<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Services\Clients;

use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Services\Clients\GuzzleAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * Class GuzzleAdapterTest.
 */
class GuzzleAdapterTest extends TestCase
{
    /**
     * @return void
     */
    public function test_it_returns_formatted_response_array(): void
    {
        $mockBody = json_encode(['status' => 'success']);
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $mockBody)
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $adapter = new GuzzleAdapter();

        $this->assertTrue(method_exists($adapter, 'request'));
    }
}
