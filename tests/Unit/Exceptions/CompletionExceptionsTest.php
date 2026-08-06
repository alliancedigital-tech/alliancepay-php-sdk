<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Tests\Unit\Exceptions;

use AlliancePay\Sdk\Exceptions\AlliancePayException;
use AlliancePay\Sdk\Exceptions\CompletionAmountException;
use AlliancePay\Sdk\Exceptions\CreateCompletionException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class CompletionExceptionsTest.
 */
class CompletionExceptionsTest extends TestCase
{
    /**
     * @return void
     */
    public function test_completion_amount_exception_extends_alliance_pay_exception(): void
    {
        $e = new CompletionAmountException('out of range');

        $this->assertInstanceOf(AlliancePayException::class, $e);
    }

    /**
     * @return void
     */
    public function test_completion_amount_exception_stores_message(): void
    {
        $message = 'completionCoinAmount 1500 is out of allowed range [800, 1200]';

        $e = new CompletionAmountException($message);

        $this->assertSame($message, $e->getMessage());
    }

    /**
     * @return void
     */
    public function test_completion_amount_exception_stores_payload(): void
    {
        $payload = [
            'originalCoinAmount' => 1000,
            'completionCoinAmount' => 1500,
            'allowedMin' => 800,
            'allowedMax' => 1200,
        ];

        $e = new CompletionAmountException('out of range', $payload);

        $this->assertSame($payload, $e->getPayload());
        $this->assertArrayHasKey('originalCoinAmount', $e->getPayload());
        $this->assertArrayHasKey('completionCoinAmount', $e->getPayload());
        $this->assertArrayHasKey('allowedMin', $e->getPayload());
        $this->assertArrayHasKey('allowedMax', $e->getPayload());
    }

    /**
     * @return void
     */
    public function test_completion_amount_exception_payload_values_are_correct(): void
    {
        $e = new CompletionAmountException('error', [
            'originalCoinAmount' => 1000,
            'completionCoinAmount' => 1201,
            'allowedMin' => 800,
            'allowedMax' => 1200,
        ]);

        $payload = $e->getPayload();
        $this->assertSame(1000, $payload['originalCoinAmount']);
        $this->assertSame(1201, $payload['completionCoinAmount']);
        $this->assertSame(800,  $payload['allowedMin']);
        $this->assertSame(1200, $payload['allowedMax']);
    }

    /**
     * @return void
     */
    public function test_completion_amount_exception_default_payload_is_empty_array(): void
    {
        $e = new CompletionAmountException('error');

        $this->assertSame([], $e->getPayload());
    }

    /**
     * @return void
     */
    public function test_completion_amount_exception_stores_code(): void
    {
        $e = new CompletionAmountException('error', [], 42);

        $this->assertSame(42, $e->getCode());
    }

    /**
     * @return void
     */
    public function test_completion_amount_exception_stores_previous(): void
    {
        $previous = new RuntimeException('root cause');
        $e = new CompletionAmountException('wrapper', [], 0, $previous);

        $this->assertSame($previous, $e->getPrevious());
    }

    /**
     * @return void
     */
    public function test_create_completion_exception_extends_alliance_pay_exception(): void
    {
        $e = new CreateCompletionException('completion failed');

        $this->assertInstanceOf(AlliancePayException::class, $e);
    }

    /**
     * @return void
     */
    public function test_create_completion_exception_stores_message(): void
    {
        $message = 'Amount validation failed: completionCoinAmount 1500 is out of range';

        $e = new CreateCompletionException($message);

        $this->assertSame($message, $e->getMessage());
    }

    /**
     * @return void
     */
    public function test_create_completion_exception_stores_payload(): void
    {
        $payload = [
            'originalCoinAmount' => 1000,
            'completionCoinAmount' => 1500,
            'allowedMin' => 800,
            'allowedMax' => 1200,
        ];

        $e = new CreateCompletionException('failed', $payload);

        $this->assertSame($payload, $e->getPayload());
    }

    /**
     * @return void
     */
    public function test_create_completion_exception_chains_previous_exception(): void
    {
        $inner   = new CompletionAmountException('root', ['originalCoinAmount' => 100]);
        $wrapper = new CreateCompletionException(
            message: 'Amount validation failed: ' . $inner->getMessage(),
            payload: $inner->getPayload(),
            code: 0,
            previous: $inner
        );

        $this->assertSame($inner, $wrapper->getPrevious());
        $this->assertInstanceOf(CompletionAmountException::class, $wrapper->getPrevious());
        $this->assertSame(100, $wrapper->getPayload()['originalCoinAmount']);
    }

    /**
     * @return void
     */
    public function test_create_completion_exception_default_payload_is_empty(): void
    {
        $e = new CreateCompletionException('error');

        $this->assertSame([], $e->getPayload());
    }
}
