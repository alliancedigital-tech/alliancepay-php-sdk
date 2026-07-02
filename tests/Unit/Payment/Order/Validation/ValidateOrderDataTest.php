<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Order\Validation;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Payment\Order\Validation\ValidateOrderData;

/**
 * Class ValidateOrderDataTest.
 */
class ValidateOrderDataTest extends TestCase
{
    private function validOrderData(): array
    {
        return [
            'merchantRequestId' => '99999999-1111-1111-9999-999999999999',
            'merchantId' => '99999999-1111-1111-9999-999999999999',
            'hppPayType' => 'PURCHASE',
            'directType' => 'REDIRECT',
            'coinAmount' => 2000,
            'paymentMethods' => ['CARD'],
            'successUrl' => 'https://example.com/success',
            'failUrl' => 'https://example.com/fail',
            'statusPageType' => 'DEFAULT',
            'customerData' => [
                'senderCustomerId' => 'cust_1',
            ],
        ];
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_throws_exception_if_required_field_missing(): void
    {
        $invalidData = [
            'merchantRequestId' => 'req_123',
        ];

        $this->expectException(ValidateDataException::class);
        $this->expectExceptionMessage('Validation error');

        ValidateOrderData::validateOrderRequiredData($invalidData);
    }

    /**
     * @return void
     */
    public function test_it_exception_payload_contains_missing_required_fields(): void
    {
        $invalidData = [
            'merchantRequestId' => 'req_123',
        ];

        try {
            ValidateOrderData::validateOrderRequiredData($invalidData);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $payload = $e->getPayload();
            $fields = array_column($payload, 'field');

            $this->assertContains('merchantId', $fields);
            $this->assertContains('hppPayType', $fields);
            $this->assertContains('coinAmount', $fields);
            $this->assertContains('paymentMethods', $fields);
            $this->assertContains('successUrl', $fields);
            $this->assertContains('failUrl', $fields);
            $this->assertContains('statusPageType', $fields);
            $this->assertContains('senderCustomerId', $fields);
        }
    }

    /**
     * @return void
     */
    public function test_it_clears_empty_additional_fields(): void
    {
        $data = $this->validOrderData();
        $data['merchantComment'] = '';
        $data['customerData']['senderFirstName'] = '';

        $result = ValidateOrderData::clearOrderData($data);

        $this->assertArrayNotHasKey('merchantComment', $result);
        $this->assertArrayNotHasKey('senderFirstName', $result['customerData']);
        $this->assertArrayHasKey('merchantId', $result);
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_field_has_wrong_type(): void
    {
        $data = $this->validOrderData();
        $data['coinAmount'] = 'not-a-number';

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $this->assertSame('Validation error', $e->getMessage());
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('coinAmount', $fields);
        }
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_numeric_string_for_integer_field(): void
    {
        $data = $this->validOrderData();
        $data['coinAmount'] = '2000';

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame('2000', $result['coinAmount']);
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_string_exceeds_max_length(): void
    {
        $data = $this->validOrderData();
        $data['merchantRequestId'] = str_repeat('a', 37);

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $this->assertSame('Validation error', $e->getMessage());
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('merchantRequestId', $fields);
        }
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_throws_exception_if_nested_required_field_missing(): void
    {
        $data = $this->validOrderData();
        $data['customerData'] = [];

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('senderCustomerId', $fields);
        }
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_boolean_for_notification_encryption(): void
    {
        $data = $this->validOrderData();
        $data['notificationEncryption'] = false;

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertFalse($result['notificationEncryption']);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_string_for_notification_encryption(): void
    {
        $data = $this->validOrderData();
        $data['notificationEncryption'] = 'RSA_KEY';

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame('RSA_KEY', $result['notificationEncryption']);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_passes_with_all_valid_required_fields(): void
    {
        $result = ValidateOrderData::validateOrderRequiredData($this->validOrderData());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('merchantRequestId', $result);
    }

    /**
     * @return void
     */
    public function test_it_clears_false_boolean_field_as_empty(): void
    {
        $data = $this->validOrderData();
        $data['generateQrNbu'] = false;

        $result = ValidateOrderData::clearOrderData($data);

        $this->assertArrayNotHasKey('generateQrNbu', $result);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_valid_iso3166_numeric_country_code(): void
    {
        $data = $this->validOrderData();
        $data['customerData']['senderCountry'] = '804';

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame('804', $result['customerData']['senderCountry']);
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_country_code_is_not_numeric(): void
    {
        $data = $this->validOrderData();
        $data['customerData']['senderCountry'] = 'Ukraine';

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('senderCountry', $fields);
        }
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_country_code_exceeds_3_digits(): void
    {
        $data = $this->validOrderData();
        $data['customerData']['senderCountry'] = '8049';

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('senderCountry', $fields);
        }
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_valid_phone_digits_only(): void
    {
        $data = $this->validOrderData();
        $data['customerData']['senderPhone'] = '380630000000';

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame('380630000000', $result['customerData']['senderPhone']);
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_phone_has_plus_prefix(): void
    {
        $data = $this->validOrderData();
        $data['customerData']['senderPhone'] = '+380630000000';

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('senderPhone', $fields);
        }
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_phone_has_spaces_or_special_chars(): void
    {
        $data = $this->validOrderData();
        $data['customerData']['senderPhone'] = '380 63 000-00-00';

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('senderPhone', $fields);
        }
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_valid_birthday_in_dmy_format(): void
    {
        $data = $this->validOrderData();
        $data['customerData']['senderBirthday'] = '31.12.2000';

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame('31.12.2000', $result['customerData']['senderBirthday']);
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_birthday_format_is_invalid(): void
    {
        $data = $this->validOrderData();
        $data['customerData']['senderBirthday'] = '2000-12-31';

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('senderBirthday', $fields);
        }
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_expiration_time_below_min(): void
    {
        $data = $this->validOrderData();
        $data['expirationTimeMinutes'] = 59;

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('expirationTimeMinutes', $fields);
        }
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_expiration_time_above_max(): void
    {
        $data = $this->validOrderData();
        $data['expirationTimeMinutes'] = 1441;

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('expirationTimeMinutes', $fields);
        }
    }

    /**
     * @dataProvider validExpirationTimeProvider
     * @param int $value
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_expiration_time_at_boundary_values(int $value): void
    {
        $data = $this->validOrderData();
        $data['expirationTimeMinutes'] = $value;

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame($value, $result['expirationTimeMinutes']);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function validExpirationTimeProvider(): array
    {
        return [
            'minimum boundary (60)' => [60],
            'maximum boundary (1440)' => [1440],
            'middle value (720)' => [720],
        ];
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_merchant_comment_empty_for_a2a(): void
    {
        $data = $this->validOrderData();
        $data['hppPayType'] = 'A2A';
        $data['directType'] = 'BANK_LINK';

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $payload = $e->getPayload();
            $fields = array_column($payload, 'field');
            $this->assertContains('merchantComment', $fields);

            $entry = array_values(array_filter($payload, fn($p) => $p['field'] === 'merchantComment'))[0];
            $this->assertStringContainsString('A2A', $entry['message']);
        }
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_merchant_comment_for_a2a(): void
    {
        $data = $this->validOrderData();
        $data['hppPayType'] = 'A2A';
        $data['directType'] = 'BANK_LINK';
        $data['merchantComment'] = 'Order comment for A2A payment';

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame('Order comment for A2A payment', $result['merchantComment']);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_does_not_require_merchant_comment_for_non_a2a(): void
    {
        $data = $this->validOrderData();
        // hppPayType = 'PURCHASE' by default, no merchantComment provided
        $data['directType'] = 'REDIRECT';

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertArrayNotHasKey('merchantComment', $result);
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_direct_type_has_invalid_variant(): void
    {
        $data = $this->validOrderData();
        $data['directType'] = 'INVALID_TYPE';

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('directType', $fields);
        }
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_valid_direct_type_bank_link(): void
    {
        $data = $this->validOrderData();
        $data['hppPayType'] = 'A2A';
        $data['directType'] = 'BANK_LINK';
        $data['merchantComment'] = 'A2A order comment';

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame('BANK_LINK', $result['directType']);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_valid_direct_type_redirect(): void
    {
        $data = $this->validOrderData();
        $data['hppPayType'] = 'PURCHASE';
        $data['directType'] = 'REDIRECT';

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame('REDIRECT', $result['directType']);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_valid_hpp_page_additional_info(): void
    {
        $data = $this->validOrderData();
        $data['hppPageAdditionalInfo'] = [
            'productsSum' => 3000,
            'products' => [
                ['name' => 'Product A', 'count' => 2, 'sum' => 1000],
                ['name' => 'Product B', 'count' => 1, 'sum' => 2000],
            ],
        ];

        $result = ValidateOrderData::validateOrderRequiredData($data);

        $this->assertSame(3000, $result['hppPageAdditionalInfo']['productsSum']);
        $this->assertCount(2, $result['hppPageAdditionalInfo']['products']);
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_product_item_missing_required_field(): void
    {
        $data = $this->validOrderData();
        $data['hppPageAdditionalInfo'] = [
            'products' => [
                ['count' => 3, 'sum' => 1000], // missing 'name'
            ],
        ];

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('products[0].name', $fields);
        }
    }

    /**
     * @return void
     */
    public function test_it_throws_exception_if_product_item_field_has_wrong_type(): void
    {
        $data = $this->validOrderData();
        $data['hppPageAdditionalInfo'] = [
            'products' => [
                ['name' => 'Product A', 'count' => 'three', 'sum' => 1000], // count must be int
            ],
        ];

        try {
            ValidateOrderData::validateOrderRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown.');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('products[0].count', $fields);
        }
    }

    /**
     * @return void
     */
    public function test_it_clears_null_hpp_page_additional_info(): void
    {
        $data = $this->validOrderData();
        $data['hppPageAdditionalInfo'] = null;

        $result = ValidateOrderData::clearOrderData($data);

        $this->assertArrayNotHasKey('hppPageAdditionalInfo', $result);
    }
}
