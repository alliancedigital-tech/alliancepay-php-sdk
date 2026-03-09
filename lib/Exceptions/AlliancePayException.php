<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Exceptions;

use Exception;
use Throwable;

/**
 * Class AlliancePayException.
 */
class AlliancePayException extends Exception implements AlliancePayExceptionInterface
{
    public function __construct(
        string $message,
        protected array $payload = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
