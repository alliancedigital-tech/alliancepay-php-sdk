<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\Clients;

use AlliancePay\Sdk\Exceptions\ApiException;
use AlliancePay\Sdk\Services\Clients\HttpBase\HttpBase;

/**
 * Class CurlAdapter.
 */
class CurlAdapter extends HttpBase implements HttpClientInterface
{
    protected string $requestId;

    /**
     * @throws ApiException
     */
    public function request(string $method, string $url, array $options = []): array
    {
        $ch = curl_init();

        if (!empty($options['query'])) {
            $url .= '?' . http_build_query($options['query']);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        $headers = [];
        foreach ($options['headers'] ?? [] as $key => $value) {
            $headers[] = "$key: $value";
        }
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (!empty($options['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options['body']));
        }

        if (!empty($options['json'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options['json']));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);

        if ($error) {
            $errno = curl_errno($ch);
            curl_close($ch);
            throw new ApiException('cURL Error (' . $errno . '): ' . $error);
        }

        curl_close($ch);

        return [
            'code'    => $httpCode,
            'body'    => json_decode($response, true),
            'headers' => [],
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
