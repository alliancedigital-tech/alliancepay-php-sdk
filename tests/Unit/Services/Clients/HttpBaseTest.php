<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Services\Clients;

use AlliancePay\Sdk\Exceptions\AuthenticationException;
use AlliancePay\Sdk\Exceptions\EncryptionException;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Services\Clients\HttpBase\HttpBase;
use AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO;

class HttpBaseTest extends TestCase
{
    private HttpBase $httpBase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->httpBase = new HttpBase();
    }

    /**
     * @return AuthorizationDTO
     * @throws AuthenticationException
     */
    protected function getAuthDtoObject(): AuthorizationDTO
    {
        return AuthorizationDTO::fromArray(
            [
                'baseUrl' => 'http://localhost',
                'merchantId' => 'merchantId',
                'serviceCode' => 'test-service-code',
                'authenticationKey' => 'authentication-key',
                'deviceId' => 'test-device-id',
                'refreshToken' => 'test-refresh-token',
            ]
        );
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_it_builds_correct_options_from_auth_dto(): void
    {
        $authDto = $this->getAuthDtoObject();

        $data = ['amount' => 1000, 'currency' => 'UAH'];

        $options = $this->httpBase->buildOptionsFromAuthDto($authDto, $data, true);

        $this->assertArrayHasKey('headers', $options);
        $headers = $options['headers'];

        $this->assertEquals('V1', $headers['x-api_version']);
        $this->assertEquals('test-device-id', $headers['x-device_id']);
        $this->assertEquals('test-refresh-token', $headers['x-refresh_token']);
        $this->assertEquals('application/json', $headers['Content-Type']);
        $this->assertArrayHasKey('json', $options);
        $this->assertEquals($data, $options['json']);
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_it_correctly_sets_content_type_for_non_json(): void
    {
        $authDto = $this->getAuthDtoObject();
        $plainData = "raw string data";

        $options = $this->httpBase->buildOptionsFromAuthDto($authDto, $plainData, false);

        $this->assertEquals('text/plain', $options['headers']['Content-Type']);
        $this->assertEquals($plainData, $options['body']);
        $this->assertArrayNotHasKey('json', $options);
    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function test_it_for_fails_without_required_params(): void
    {
        $this->expectException(AuthenticationException::class);
        AuthorizationDTO::fromArray(
            [
                'baseUrl' => 'http://localhost',
                'merchantId' => 'merchantId'
            ]
        );
    }
}
