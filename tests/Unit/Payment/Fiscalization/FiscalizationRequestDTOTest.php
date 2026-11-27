<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Fiscalization;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Dto\Fiscalization\FiscalizationProductDTO;
use AlliancePay\Sdk\Payment\Dto\Fiscalization\FiscalizationRequestDTO;
use AlliancePay\Sdk\Traits\CoinAmountConverter;
use PHPUnit\Framework\TestCase;

/**
 * Class FiscalizationRequestDTOTest.
 */
class FiscalizationRequestDTOTest extends TestCase
{
    private function validData(array $productOverrides = [], array $overrides = []): array
    {
        return array_merge([
            'merchantRequestId' => '8d2d1234-5678-4e92-a975-672f42fca5e3',
            'merchantId' => 'M1',
            'operationId' => 'OP-1',
            'receiptComment' => 'PAYMENT_FOR_ORDER_123',
            'notificationUrl' => 'https://merchant.example.com/fiscalization/callback',
            'products' => [array_merge([
                'id' => 'sku-1',
                'name' => 'Товар 1',
                'count' => 2,
                'measurementCode' => '0001',
                'unitPrice' => 120,
                'taxCode' => 2,
                'classificationCode' => '2203000000',
                'exciseLabels' => [' AATE123456 ', ''],
            ], $productOverrides)],
        ], $overrides);
    }

    public function test_to_array_builds_expected_payload(): void
    {
        $dto = FiscalizationRequestDTO::fromArray($this->validData());

        $this->assertSame([
            'merchantRequestId' => '8d2d1234-5678-4e92-a975-672f42fca5e3',
            'merchantId' => 'M1',
            'operationId' => 'OP-1',
            'notificationUrl' => 'https://merchant.example.com/fiscalization/callback',
            'products' => [[
                'id' => 'sku-1',
                'name' => 'Товар 1',
                'count' => 2.0,
                'measurementCode' => '0001',
                'unitPrice' => 120.0,
                'taxCode' => 2,
                'classificationCode' => '2203000000',
                'exciseLabels' => ['AATE123456'],
            ]],
            'receiptComment' => 'PAYMENT_FOR_ORDER_123',
        ], $dto->toArray());
    }

    public function test_optional_fields_are_omitted_when_empty(): void
    {
        $data = $this->validData(
            ['taxCode' => null, 'classificationCode' => null, 'exciseLabels' => []],
            ['receiptComment' => null]
        );

        $array = FiscalizationRequestDTO::fromArray($data)->toArray();

        $this->assertArrayNotHasKey('receiptComment', $array);
        $this->assertArrayNotHasKey('taxCode', $array['products'][0]);
        $this->assertArrayNotHasKey('classificationCode', $array['products'][0]);
        $this->assertArrayNotHasKey('exciseLabels', $array['products'][0]);
    }

    public function test_unit_price_can_be_built_from_kopecks_with_existing_converter(): void
    {
        $product = new FiscalizationProductDTO(
            id: '1',
            name: 'x',
            count: 1,
            measurementCode: '0001',
            unitPrice: (new class {
                use CoinAmountConverter;
            })::convertFromUahCoins(12050)
        );

        $this->assertSame(120.5, $product->toArray()['unitPrice']);
    }

    public function test_amounts_are_numeric_in_json(): void
    {
        // json_encode() writes 120.0 as `120` — still a JSON number, equal to 120.00 for the API.
        $decoded = json_decode(
            json_encode(FiscalizationRequestDTO::fromArray($this->validData())->toArray()),
            true
        );

        $this->assertSame(120, $decoded['products'][0]['unitPrice']);
        $this->assertEquals(2.0, $decoded['products'][0]['count']);
    }

    public function test_missing_required_fields_throw(): void
    {
        $this->expectException(ValidateDataException::class);
        $this->expectExceptionMessage('notificationUrl');

        $data = $this->validData();
        unset($data['notificationUrl']);
        FiscalizationRequestDTO::fromArray($data);
    }

    public function test_relative_notification_url_is_rejected(): void
    {
        $this->expectException(ValidateDataException::class);
        $this->expectExceptionMessage('absolute URL');

        FiscalizationRequestDTO::fromArray($this->validData([], ['notificationUrl' => '/callback']));
    }

    public function test_empty_products_throw(): void
    {
        $this->expectException(ValidateDataException::class);
        $this->expectExceptionMessage('products');

        FiscalizationRequestDTO::fromArray($this->validData([], ['products' => []]));
    }

    /**
     * @dataProvider classificationTaxCodes
     */
    public function test_classification_code_is_required_for_tax_codes_2_7_8(int $taxCode): void
    {
        $this->expectException(ValidateDataException::class);
        $this->expectExceptionMessage('classificationCode is required');

        FiscalizationRequestDTO::fromArray($this->validData(['taxCode' => $taxCode, 'classificationCode' => null]));
    }

    public static function classificationTaxCodes(): array
    {
        return [[2], [7], [8]];
    }

    public function test_classification_code_is_not_required_for_other_tax_codes(): void
    {
        $dto = FiscalizationRequestDTO::fromArray($this->validData(['taxCode' => 1, 'classificationCode' => null]));

        $this->assertSame(1, $dto->getProducts()[0]->getTaxCode());
    }

    public function test_classification_code_must_be_digits(): void
    {
        $this->expectException(ValidateDataException::class);
        $this->expectExceptionMessage('digits only');

        FiscalizationRequestDTO::fromArray($this->validData(['classificationCode' => '22AB']));
    }
}
