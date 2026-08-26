<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Payment\Dto\Operations;

use AlliancePay\Sdk\Payment\Dto\Operations\OperationPurchaseDTO;
use Exception;
use PHPUnit\Framework\TestCase;

class OperationPurchaseDTOTest extends TestCase
{
    /**
     * @return array
     */
    private function getBaseOperationData(): array
    {
        return [
            'type' => OperationPurchaseDTO::OPERATION_TYPE,
            'rrn' => 'testRrn',
            'coinAmount' => 18000,
            'merchantId' => 'testMerchantId',
            'operationId' => 'testOperationId',
            'ecomOperationId' => 'testEcomOperationId',
            'status' => 'SUCCESS',
            'transactionCurrency' => '980',
            'creationDateTime' => '2026.01.23 11:11:59.903',
            'modificationDateTime' => '2026.01.23 11:12:00.174',
            'transactionResponseInfo' => [],
            'notificationEncryption' => false,
            'notificationSignature' => false,
            'hppOrderId' => 'testHppOrderId',
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_hydrates_source_amount(): void
    {
        $data = $this->getBaseOperationData();
        $data['sourceAmount'] = 1000;

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(1000, $dto->getSourceAmount());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_hydrates_source_currency_code(): void
    {
        $data = $this->getBaseOperationData();
        $data['sourceCurrencyCode'] = 840;

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(840, $dto->getSourceCurrencyCode());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_hydrates_conversion_rate(): void
    {
        $data = $this->getBaseOperationData();
        $data['conversionRate'] = 39.50;

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(39.50, $dto->getConversionRate());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_hydrates_all_new_fields(): void
    {
        $data = $this->getBaseOperationData();
        $data['sourceAmount'] = 1000;
        $data['sourceCurrencyCode'] = 840;
        $data['conversionRate'] = 39.50;

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(1000, $dto->getSourceAmount());
        $this->assertSame(840, $dto->getSourceCurrencyCode());
        $this->assertSame(39.50, $dto->getConversionRate());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_source_amount_defaults_to_null(): void
    {
        $data = $this->getBaseOperationData();

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertNull($dto->getSourceAmount());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_source_currency_code_defaults_to_null(): void
    {
        $data = $this->getBaseOperationData();

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertNull($dto->getSourceCurrencyCode());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_conversion_rate_defaults_to_null(): void
    {
        $data = $this->getBaseOperationData();

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertNull($dto->getConversionRate());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_converts_source_amount_to_int(): void
    {
        $data = $this->getBaseOperationData();
        $data['sourceAmount'] = '2500';

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(2500, $dto->getSourceAmount());
        $this->assertIsInt($dto->getSourceAmount());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_converts_source_currency_code_to_int(): void
    {
        $data = $this->getBaseOperationData();
        $data['sourceCurrencyCode'] = '978';

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(978, $dto->getSourceCurrencyCode());
        $this->assertIsInt($dto->getSourceCurrencyCode());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_converts_conversion_rate_to_float(): void
    {
        $data = $this->getBaseOperationData();
        $data['conversionRate'] = '42.75';

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(42.75, $dto->getConversionRate());
        $this->assertIsFloat($dto->getConversionRate());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_to_array_includes_new_fields(): void
    {
        $data = $this->getBaseOperationData();
        $data['sourceAmount'] = 1000;
        $data['sourceCurrencyCode'] = 840;
        $data['conversionRate'] = 39.50;

        $dto = OperationPurchaseDTO::fromArray($data);
        $result = $dto->toArray();

        $this->assertArrayHasKey('sourceAmount', $result);
        $this->assertArrayHasKey('sourceCurrencyCode', $result);
        $this->assertArrayHasKey('conversionRate', $result);
        $this->assertSame(1000, $result['sourceAmount']);
        $this->assertSame(840, $result['sourceCurrencyCode']);
        $this->assertSame(39.50, $result['conversionRate']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_to_array_includes_null_values_for_new_fields(): void
    {
        $data = $this->getBaseOperationData();

        $dto = OperationPurchaseDTO::fromArray($data);
        $result = $dto->toArray();

        $this->assertArrayHasKey('sourceAmount', $result);
        $this->assertArrayHasKey('sourceCurrencyCode', $result);
        $this->assertArrayHasKey('conversionRate', $result);
        $this->assertNull($result['sourceAmount']);
        $this->assertNull($result['sourceCurrencyCode']);
        $this->assertNull($result['conversionRate']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_with_usd_currency_example(): void
    {
        $data = $this->getBaseOperationData();
        $data['sourceAmount'] = 1000;
        $data['sourceCurrencyCode'] = 840;
        $data['conversionRate'] = 39.50;
        $data['coinAmount'] = 39500;

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(1000, $dto->getSourceAmount());
        $this->assertSame(840, $dto->getSourceCurrencyCode());
        $this->assertSame(39.50, $dto->getConversionRate());
        $this->assertSame(39500, $dto->getCoinAmount());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_with_eur_currency_example(): void
    {
        $data = $this->getBaseOperationData();
        $data['sourceAmount'] = 2500;
        $data['sourceCurrencyCode'] = 978;
        $data['conversionRate'] = 42.75;
        $data['coinAmount'] = 106875;

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(2500, $dto->getSourceAmount());
        $this->assertSame(978, $dto->getSourceCurrencyCode());
        $this->assertSame(42.75, $dto->getConversionRate());
        $this->assertSame(106875, $dto->getCoinAmount());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_with_zero_conversion_rate(): void
    {
        $data = $this->getBaseOperationData();
        $data['conversionRate'] = 0.0;

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(0.0, $dto->getConversionRate());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_from_array_with_high_precision_conversion_rate(): void
    {
        $data = $this->getBaseOperationData();
        $data['conversionRate'] = 39.123456;

        $dto = OperationPurchaseDTO::fromArray($data);

        $this->assertSame(39.123456, $dto->getConversionRate());
    }
}
