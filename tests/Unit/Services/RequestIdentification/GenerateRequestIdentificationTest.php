<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Tests\Unit\Services\RequestIdentification;

use Exception;
use PHPUnit\Framework\TestCase;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;

/**
 * Class GenerateRequestIdentificationTest.
 */
class GenerateRequestIdentificationTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function test_it_returns_non_empty_string(): void
    {
        $id = GenerateRequestIdentification::generateRequestId();

        $this->assertIsString($id);
        $this->assertNotEmpty($id);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_it_generates_unique_ids(): void
    {
        $id1 = GenerateRequestIdentification::generateRequestId();
        $id2 = GenerateRequestIdentification::generateRequestId();
        $id3 = GenerateRequestIdentification::generateRequestId();

        $this->assertNotEquals($id1, $id2);
        $this->assertNotEquals($id2, $id3);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_it_follows_uuid_v4_format(): void
    {
        $id = GenerateRequestIdentification::generateRequestId();

        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

        $this->assertMatchesRegularExpression($pattern, $id);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_it_has_correct_length(): void
    {
        $id = GenerateRequestIdentification::generateRequestId();

        $this->assertEquals(36, strlen($id));
    }
}
