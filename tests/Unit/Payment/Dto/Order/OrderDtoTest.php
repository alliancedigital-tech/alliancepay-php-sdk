<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Payment\Dto\Order;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;
use AlliancePay\Sdk\Payment\Dto\Order\OrderResponseDTO;
use AlliancePay\Sdk\Exceptions\ValidateDataException;

class OrderDtoTest extends TestCase
{
    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_order_request_dto_mapping(): void
    {
        $data = [
            'merchantRequestId' => 'REQ-123',
            'merchantId' => 'M-777',
            'hppPayType' => 'PURCHASE',
            'directType' => 'REDIRECT',
            'coinAmount' => 15050,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://site.com/success',
            'failUrl' => 'https://site.com/fail',
            'statusPageType' => 'REDIRECT',
            'customerData' => ['senderCustomerId' => 'id_1'],
            'merchantComment' => 'Test Order',
            'hppPageAdditionalInfo' => [
                'productsSum' => 5000,
                'products' => [
                    ['name' => 'Widget', 'count' => 2, 'sum' => 2500],
                    ['name' => 'Gadget', 'count' => 1, 'sum' => 2500],
                ],
            ],
        ];

        $dto = OrderRequestDTO::fromArray($data);

        $this->assertEquals('REQ-123', $dto->getMerchantRequestId());
        $this->assertEquals(15050, $dto->getCoinAmount());
        $this->assertArrayHasKey('senderCustomerId', $dto->toArray()['customerData']);
        $this->assertSame(5000, $dto->getHppPageAdditionalInfo()['productsSum']);
        $this->assertCount(2, $dto->getHppPageAdditionalInfo()['products']);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_order_request_dto_to_array_includes_hpp_page_additional_info(): void
    {
        $hppInfo = [
            'productsSum' => 3000,
            'products' => [
                ['name' => 'Item', 'count' => 3, 'sum' => 1000],
            ],
        ];

        $data = [
            'merchantRequestId' => 'REQ-456',
            'merchantId' => 'M-888',
            'hppPayType' => 'PURCHASE',
            'directType' => 'REDIRECT',
            'coinAmount' => 3000,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://site.com/success',
            'failUrl' => 'https://site.com/fail',
            'statusPageType' => 'REDIRECT',
            'customerData' => ['senderCustomerId' => 'id_2'],
            'hppPageAdditionalInfo' => $hppInfo,
        ];

        $dto = OrderRequestDTO::fromArray($data);
        $result = $dto->toArray();

        $this->assertArrayHasKey('hppPageAdditionalInfo', $result);
        $this->assertSame(3000, $result['hppPageAdditionalInfo']['productsSum']);
        $this->assertSame('Item', $result['hppPageAdditionalInfo']['products'][0]['name']);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_preauth_exp_date_auto_set_when_hpp_pay_type_is_preauth_and_date_not_provided(): void
    {
        $data = [
            'merchantRequestId' => 'REQ-PA-1',
            'merchantId' => 'M-1',
            'hppPayType' => 'PREAUTH',
            'directType' => 'REDIRECT',
            'coinAmount' => 1000,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://example.com/success',
            'failUrl' => 'https://example.com/fail',
            'statusPageType' => 'STATUS_PAGE',
            'customerData' => ['senderCustomerId' => 'cust_1'],
        ];

        $dto = OrderRequestDTO::fromArray($data);

        $this->assertNotNull($dto->getPreAuthExpDate());
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_preauth_exp_date_auto_value_has_correct_format(): void
    {
        $data = [
            'merchantRequestId' => 'REQ-PA-2',
            'merchantId' => 'M-1',
            'hppPayType' => 'PREAUTH',
            'directType' => 'REDIRECT',
            'coinAmount' => 1000,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://example.com/success',
            'failUrl' => 'https://example.com/fail',
            'statusPageType' => 'STATUS_PAGE',
            'customerData' => ['senderCustomerId' => 'cust_1'],
        ];

        $dto = OrderRequestDTO::fromArray($data);

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{2}[+-]\d{2}:\d{2}$/',
            $dto->getPreAuthExpDate()
        );
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_preauth_exp_date_auto_value_is_approximately_now_plus_two_hours(): void
    {
        $data = [
            'merchantRequestId' => 'REQ-PA-3',
            'merchantId' => 'M-1',
            'hppPayType' => 'PREAUTH',
            'directType' => 'REDIRECT',
            'coinAmount' => 1000,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://example.com/success',
            'failUrl' => 'https://example.com/fail',
            'statusPageType' => 'STATUS_PAGE',
            'customerData' => ['senderCustomerId' => 'cust_1'],
        ];

        $before = new DateTimeImmutable('now');
        $dto = OrderRequestDTO::fromArray($data);
        $after = new DateTimeImmutable('now');

        $parsed = new DateTimeImmutable($dto->getPreAuthExpDate());

        $minExpected = $before->modify('+2 hours +25 seconds');
        $maxExpected = $after->modify('+2 hours +35 seconds');

        $this->assertGreaterThanOrEqual($minExpected->getTimestamp(), $parsed->getTimestamp());
        $this->assertLessThanOrEqual($maxExpected->getTimestamp(), $parsed->getTimestamp());
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_preauth_exp_date_is_null_for_purchase_type(): void
    {
        $data = [
            'merchantRequestId' => 'REQ-PUR-1',
            'merchantId' => 'M-1',
            'hppPayType' => 'PURCHASE',
            'directType' => 'REDIRECT',
            'coinAmount' => 1000,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://example.com/success',
            'failUrl' => 'https://example.com/fail',
            'statusPageType' => 'STATUS_PAGE',
            'customerData' => ['senderCustomerId' => 'cust_1'],
        ];

        $dto = OrderRequestDTO::fromArray($data);

        $this->assertNull($dto->getPreAuthExpDate());
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_preauth_exp_date_provided_value_is_used_when_not_null(): void
    {
        $futureDate = (new DateTimeImmutable('now'))->modify('+5 hours')->format('Y-m-d H:i:s.') .
            '00+00:00';

        $data = [
            'merchantRequestId' => 'REQ-PA-4',
            'merchantId' => 'M-1',
            'hppPayType' => 'PREAUTH',
            'directType' => 'REDIRECT',
            'coinAmount' => 1000,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://example.com/success',
            'failUrl' => 'https://example.com/fail',
            'statusPageType' => 'STATUS_PAGE',
            'customerData' => ['senderCustomerId' => 'cust_1'],
            'preAuthExpDate' => $futureDate,
        ];

        $dto = OrderRequestDTO::fromArray($data);

        $this->assertSame($futureDate, $dto->getPreAuthExpDate());
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_preauth_exp_date_included_in_to_array_when_preauth(): void
    {
        $data = [
            'merchantRequestId' => 'REQ-PA-5',
            'merchantId' => 'M-1',
            'hppPayType' => 'PREAUTH',
            'directType' => 'REDIRECT',
            'coinAmount' => 1000,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://example.com/success',
            'failUrl' => 'https://example.com/fail',
            'statusPageType' => 'STATUS_PAGE',
            'customerData' => ['senderCustomerId' => 'cust_1'],
        ];

        $dto = OrderRequestDTO::fromArray($data);
        $result = $dto->toArray();

        $this->assertArrayHasKey('preAuthExpDate', $result);
        $this->assertNotNull($result['preAuthExpDate']);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function test_order_response_dto_dates(): void
    {
        $data = [
            'hppOrderId' => 'HPP-1',
            'merchantRequestId' => 'REQ-1',
            'hppPayType' => 'CARD',
            'paymentMethods' => ['CARD'],
            'orderStatus' => 'PENDING',
            'coinAmount' => 100,
            'merchantId' => 'M1',
            'expiredOrderDate' => '2026.02.20 12:00:00.000',
            'redirectUrl' => 'https://pay.ua',
            'createDate' => '2026.02.16 10:00:00.000',
            'statusUrl' => 'https://status.ua'
        ];

        $dto = OrderResponseDTO::fromArray($data);

        $this->assertInstanceOf(DateTimeImmutable::class, $dto->getCreateDate());
        $this->assertEquals('2026-02-16', $dto->getCreateDate()->format('Y-m-d'));
    }
}
