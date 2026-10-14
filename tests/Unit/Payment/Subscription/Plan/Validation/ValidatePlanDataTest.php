<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Payment\Subscription\Plan\Validation;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Subscription\Plan\Validation\ValidatePlanData;
use PHPUnit\Framework\TestCase;

class ValidatePlanDataTest extends TestCase
{
    /**
     * @return array
     */
    private function getBasePlanData(): array
    {
        return [
            'name' => 'Monthly subscription',
            'description' => 'Monthly subscription plan',
            'categorySubcategoryIndicator' => 'C101',
            'merchantId' => 'merchant-1',
            'rules' => [
                [
                    'paymentNumberFrom' => 1,
                    'paymentNumberTo' => 1,
                    'intervalValue' => 1,
                    'intervalUnit' => 'MONTH',
                    'coinAmount' => 0,
                    'currency' => '980',
                ],
                [
                    'paymentNumberFrom' => 2,
                    'paymentNumberTo' => 999,
                    'intervalValue' => 1,
                    'intervalUnit' => 'MONTH',
                    'coinAmount' => 10000,
                    'currency' => '980',
                ],
            ],
        ];
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_valid_plan_data_with_trial_rule(): void
    {
        $result = ValidatePlanData::validatePlanRequiredData($this->getBasePlanData());

        $this->assertSame('Monthly subscription', $result['name']);
    }

    /**
     * @return void
     */
    public function test_it_rejects_missing_required_top_level_field(): void
    {
        $data = $this->getBasePlanData();
        unset($data['merchantId']);

        $this->expectException(ValidateDataException::class);

        ValidatePlanData::validatePlanRequiredData($data);
    }

    /**
     * @return void
     */
    public function test_it_rejects_rule_where_payment_number_from_exceeds_to(): void
    {
        $data = $this->getBasePlanData();
        $data['rules'][1]['paymentNumberFrom'] = 500;
        $data['rules'][1]['paymentNumberTo'] = 100;

        try {
            ValidatePlanData::validatePlanRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('rules[1].paymentNumberFrom', $fields);
        }
    }

    /**
     * @return void
     */
    public function test_it_rejects_overlapping_rule_ranges(): void
    {
        $data = $this->getBasePlanData();
        $data['rules'][1]['paymentNumberFrom'] = 1;
        $data['rules'][1]['paymentNumberTo'] = 999;

        $this->expectException(ValidateDataException::class);

        ValidatePlanData::validatePlanRequiredData($data);
    }

    /**
     * @return void
     */
    public function test_it_rejects_unsupported_currency(): void
    {
        $data = $this->getBasePlanData();
        $data['rules'][1]['currency'] = '840';

        try {
            ValidatePlanData::validatePlanRequiredData($data);
            $this->fail('Expected ValidateDataException was not thrown');
        } catch (ValidateDataException $e) {
            $fields = array_column($e->getPayload(), 'field');
            $this->assertContains('rules[1].currency', $fields);
        }
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_it_accepts_explicit_uah_currency(): void
    {
        $data = $this->getBasePlanData();
        $data['rules'][1]['currency'] = '980';

        $result = ValidatePlanData::validatePlanRequiredData($data);

        $this->assertSame('980', $result['rules'][1]['currency']);
    }

    /**
     * @return void
     * @throws ValidateDataException
     */
    public function test_clear_plan_data_removes_empty_optional_fields(): void
    {
        $data = $this->getBasePlanData();
        $data['appliesDateTimeFrom'] = '';
        $data['planId'] = null;

        $cleared = ValidatePlanData::clearPlanData($data);

        $this->assertArrayNotHasKey('appliesDateTimeFrom', $cleared);
        $this->assertArrayNotHasKey('planId', $cleared);
    }
}
