<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Fiscalization;

use AlliancePay\Sdk\Exceptions\ValidateDataException;
use AlliancePay\Sdk\Payment\Fiscalization\Validation\ValidateFiscalizationData;

/**
 * Class FiscalizationProductDTO.
 */
final class FiscalizationProductDTO
{
    public const PROPERTY_ID = 'id';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_COUNT = 'count';
    public const PROPERTY_MEASUREMENT_CODE = 'measurementCode';
    public const PROPERTY_UNIT_PRICE = 'unitPrice';
    public const PROPERTY_TAX_CODE = 'taxCode';
    public const PROPERTY_CLASSIFICATION_CODE = 'classificationCode';
    public const PROPERTY_EXCISE_LABELS = 'exciseLabels';

    /**
     * @param string $id
     * @param string $name
     * @param float $count
     * @param string $measurementCode
     * @param float $unitPrice
     * @param int|null $taxCode
     * @param string|null $classificationCode
     * @param string[] $exciseLabels
     */
    public function __construct(
        private string $id,
        private string $name,
        private float $count,
        private string $measurementCode,
        private float $unitPrice,
        private ?int $taxCode = null,
        private ?string $classificationCode = null,
        private array $exciseLabels = []
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getCount(): float
    {
        return $this->count;
    }

    /**
     * @return string
     */
    public function getMeasurementCode(): string
    {
        return $this->measurementCode;
    }

    /**
     * @return float
     */
    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    /**
     * @return int|null
     */
    public function getTaxCode(): ?int
    {
        return $this->taxCode;
    }

    /**
     * @return string|null
     */
    public function getClassificationCode(): ?string
    {
        return $this->classificationCode;
    }

    /**
     * @return string[]
     */
    public function getExciseLabels(): array
    {
        return $this->exciseLabels;
    }

    /**
     * @param array $data
     * @return self
     * @throws ValidateDataException
     */
    public static function fromArray(array $data): self
    {
        $errors = ValidateFiscalizationData::validateProductData($data);

        if ($errors !== []) {
            throw new ValidateDataException(implode(' ', $errors));
        }

        $taxCode = $data[self::PROPERTY_TAX_CODE] ?? null;
        $classificationCode = $data[self::PROPERTY_CLASSIFICATION_CODE] ?? null;

        return new self(
            id: (string)$data[self::PROPERTY_ID],
            name: (string)$data[self::PROPERTY_NAME],
            count: (float)$data[self::PROPERTY_COUNT],
            measurementCode: (string)$data[self::PROPERTY_MEASUREMENT_CODE],
            unitPrice: (float)$data[self::PROPERTY_UNIT_PRICE],
            taxCode: ($taxCode === null || $taxCode === '') ? null : (int)$taxCode,
            classificationCode: ($classificationCode === null || $classificationCode === '')
                ? null
                : (string)$classificationCode,
            exciseLabels: self::normalizeExciseLabels($data[self::PROPERTY_EXCISE_LABELS] ?? [])
        );
    }

    /**
     * Null fields are omitted; `exciseLabels` is sent only as a non-empty array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            self::PROPERTY_ID => $this->id,
            self::PROPERTY_NAME => $this->name,
            self::PROPERTY_COUNT => round($this->count, 3),
            self::PROPERTY_MEASUREMENT_CODE => $this->measurementCode,
            self::PROPERTY_UNIT_PRICE => round($this->unitPrice, 2),
        ];

        if ($this->taxCode !== null) {
            $data[self::PROPERTY_TAX_CODE] = $this->taxCode;
        }

        if ($this->classificationCode !== null) {
            $data[self::PROPERTY_CLASSIFICATION_CODE] = $this->classificationCode;
        }

        if ($this->exciseLabels !== []) {
            $data[self::PROPERTY_EXCISE_LABELS] = $this->exciseLabels;
        }

        return $data;
    }

    /**
     * @param mixed $labels
     * @return string[]
     */
    private static function normalizeExciseLabels(mixed $labels): array
    {
        if (!is_array($labels)) {
            return [];
        }

        $result = [];

        foreach ($labels as $label) {
            $label = trim((string)$label);

            if ($label !== '') {
                $result[] = $label;
            }
        }

        return $result;
    }
}
