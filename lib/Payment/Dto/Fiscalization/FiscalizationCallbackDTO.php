<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Fiscalization;

use AlliancePay\Sdk\Exceptions\CallbackException;

/**
 * Class FiscalizationCallbackDTO.
 */
final class FiscalizationCallbackDTO
{
    public function __construct(
        private FiscalizationResponseDTO $fiscalization,
        private array $rawPayload
    ) {
    }

    /**
     * @param array $data
     * @return self
     * @throws CallbackException
     */
    public static function fromArray(array $data): self
    {
        $fiscalization = FiscalizationResponseDTO::fromArray($data);

        if ($fiscalization->getOperationId() === '' && $fiscalization->getMerchantRequestId() === '') {
            throw new CallbackException('Fiscalization callback has neither operationId nor merchantRequestId.');
        }

        return new self($fiscalization, $data);
    }

    public function getFiscalization(): FiscalizationResponseDTO
    {
        return $this->fiscalization;
    }

    public function getRawPayload(): array
    {
        return $this->rawPayload;
    }

    public function toArray(): array
    {
        return $this->fiscalization->toArray();
    }
}
