<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Clients;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Services\Clients\HttpBase\HttpBase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class GuzzleAdapter.
 */
class GuzzleAdapter extends HttpBase implements HttpClientInterface
{
    private Client $client;

    protected string $requestId;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return array
     * @throws ApiException
     */
    public function request(string $method, string $url, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $url, $options);
        } catch (GuzzleException $e) {
            throw new ApiException('Guzzle Request Error: ' . $e->getMessage());
        }

        return [
            'code'    => $response->getStatusCode(),
            'body'    => json_decode($response->getBody()->getContents(), true),
            'headers' => $response->getHeaders(),
        ];
    }

    /**
     * @param string $requestId
     * @return void
     */
    public function applyRequestId(string $requestId): void
    {
        $this->requestId = $requestId;
    }
}
