<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Payment\Dto\Operations;

use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use DateTimeImmutable;
use Exception;

/**
 * Class OperationPurchaseDTO.
 */
class OperationPurchaseDTO
{
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_RRN = 'rrn';
    public const PROPERTY_PURPOSE = 'purpose';
    public const PROPERTY_COIN_AMOUNT = 'coinAmount';
    public const PROPERTY_MERCHANT_ID = 'merchantId';
    public const PROPERTY_OPERATION_ID = 'operationId';
    public const PROPERTY_ECOM_OPERATION_ID = 'ecomOperationId';
    public const PROPERTY_MERCHANT_NAME = 'merchantName';
    public const PROPERTY_APPROVAL_CODE = 'approvalCode';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_TRANSACTION_TYPE = 'transactionType';
    public const PROPERTY_TRANSACTION_CURRENCY = 'transactionCurrency';
    public const PROPERTY_CREATION_DATE_TIME = 'creationDateTime';
    public const PROPERTY_MODIFICATION_DATE_TIME = 'modificationDateTime';
    public const PROPERTY_PROCESSING_DATE_TIME = 'processingDateTime';
    public const PROPERTY_TRANSACTION_RESPONSE_INFO = 'transactionResponseInfo';
    public const PROPERTY_BANK_CODE = 'bankCode';
    public const PROPERTY_PAYMENT_SYSTEM = 'paymentSystem';
    public const PROPERTY_PAYMENT_SERVICE_TYPE = 'paymentServiceType';
    public const PROPERTY_NOTIFICATION_ENCRYPTION = 'notificationEncryption';
    public const PROPERTY_NOTIFICATION_SIGNATURE = 'notificationSignature';
    public const PROPERTY_HPP_ORDER_ID = 'hppOrderId';
    public const PROPERTY_PROCESSING_TERMINAL_ID = 'processingTerminalId';
    public const PROPERTY_PROCESSING_MERCHANT_ID = 'processingMerchantId';
    public const PROPERTY_CREATOR_SYSTEM = 'creatorSystem';
    public const PROPERTY_CARD_NUMBER_MASK = 'cardNumberMask';
    public const PROPERTY_DESIRED_THREE_DS_MODE = 'desiredThreeDSMode';
    public const PROPERTY_THREE_DS_MODE = 'threeDSMode';
    public const PROPERTY_ACS_TRANS_ID = 'acsTransId';
    public const PROPERTY_DS_TRANS_ID = 'dsTransId';
    public const PROPERTY_ECI = 'eci';
    public const PROPERTY_STATUS_THREE_DS = 'statusThreeDs';
    public const PROPERTY_THREE_DS_SERVER_TRANS_ID = 'threeDSServerTransId';
    public const PROPERTY_SENDER_CUSTOMER_ID = 'senderCustomerId';
    public const PROPERTY_SENDER_FIRST_NAME = 'senderFirstName';
    public const PROPERTY_SENDER_LAST_NAME = 'senderLastName';
    public const PROPERTY_SENDER_MIDDLE_NAME = 'senderMiddleName';
    public const PROPERTY_SENDER_EMAIL = 'senderEmail';
    public const PROPERTY_SENDER_COUNTRY = 'senderCountry';
    public const PROPERTY_SENDER_REGION = 'senderRegion';
    public const PROPERTY_SENDER_CITY = 'senderCity';
    public const PROPERTY_SENDER_STREET = 'senderStreet';
    public const PROPERTY_SENDER_ADDITIONAL_ADDRESS = 'senderAdditionalAddress';
    public const PROPERTY_SENDER_ITN = 'senderItn';
    public const PROPERTY_SENDER_PASSPORT = 'senderPassport';
    public const PROPERTY_SENDER_IP = 'senderIp';
    public const PROPERTY_SENDER_PHONE = 'senderPhone';
    public const PROPERTY_SENDER_BIRTHDAY = 'senderBirthday';
    public const PROPERTY_SENDER_GENDER = 'senderGender';
    public const PROPERTY_SENDER_ZIP_CODE = 'senderZipCode';
    public const PROPERTY_SENDER_BANK_CODE = 'senderBankCode';
    public const PROPERTY_SENDER_PAYMENT_SYSTEM = 'senderPaymentSystem';
    public const PROPERTY_SENDER_CARD_NUMBER_MASK = 'senderCardNumberMask';
    public const PROPERTY_RECEIPT_URL = 'receiptUrl';
    public const OPERATION_TYPE = 'PURCHASE';

    public function __construct(
        private string $type,
        private string $rrn,
        private ?string $purpose,
        private int $coinAmount,
        private string $merchantId,
        private string $operationId,
        private string $ecomOperationId,
        private ?string $merchantName,
        private ?string $approvalCode,
        private string $status,
        private ?int $transactionType,
        private string $transactionCurrency,
        private ?DateTimeImmutable $creationDateTime,
        private ?DateTimeImmutable $modificationDateTime,
        private ?DateTimeImmutable $processingDateTime,
        private array $transactionResponseInfo,
        private ?string $bankCode,
        private ?string $paymentSystem,
        private ?string $paymentServiceType,
        private bool $notificationEncryption,
        private bool $notificationSignature,
        private string $hppOrderId,
        private ?string $processingTerminalId,
        private ?string $processingMerchantId,
        private ?string $creatorSystem,
        private ?string $cardNumberMask,
        private ?string $desiredThreeDSMode,
        private ?string $threeDSMode,
        private ?string $acsTransId,
        private ?string $dsTransId,
        private ?string $eci,
        private ?string $statusThreeDs,
        private ?string $threeDSServerTransId,
        private ?string $senderCustomerId,
        private ?string $senderFirstName,
        private ?string $senderLastName,
        private ?string $senderMiddleName,
        private ?string $senderEmail,
        private ?string $senderCountry,
        private ?string $senderRegion,
        private ?string $senderCity,
        private ?string $senderStreet,
        private ?string $senderAdditionalAddress,
        private ?string $senderItn,
        private ?string $senderPassport,
        private ?string $senderIp,
        private ?string $senderPhone,
        private ?string $senderBirthday,
        private ?string $senderGender,
        private ?string $senderZipCode,
        private ?string $senderBankCode,
        private ?string $senderPaymentSystem,
        private ?string $senderCardNumberMask,
        private ?string $receiptUrl
    ) {}

    /**
     * @param array $data
     * @return self
     * @throws Exception
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data[self::PROPERTY_TYPE] ?? '',
            rrn: $data[self::PROPERTY_RRN] ?? '',
            purpose: $data[self::PROPERTY_PURPOSE] ?? null,
            coinAmount: (int)($data[self::PROPERTY_COIN_AMOUNT] ?? 0),
            merchantId: $data[self::PROPERTY_MERCHANT_ID] ?? '',
            operationId: $data[self::PROPERTY_OPERATION_ID] ?? '',
            ecomOperationId: $data[self::PROPERTY_ECOM_OPERATION_ID] ?? '',
            merchantName: $data[self::PROPERTY_MERCHANT_NAME] ?? null,
            approvalCode: $data[self::PROPERTY_APPROVAL_CODE] ?? null,
            status: $data[self::PROPERTY_STATUS] ?? '',
            transactionType: isset($data[self::PROPERTY_TRANSACTION_TYPE])
                ? (int)$data[self::PROPERTY_TRANSACTION_TYPE] : null,
            transactionCurrency: $data[self::PROPERTY_TRANSACTION_CURRENCY] ?? '',
            creationDateTime: DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_CREATION_DATE_TIME],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ),
            modificationDateTime: DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_MODIFICATION_DATE_TIME],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE)
            ,
            processingDateTime: isset($data[self::PROPERTY_PROCESSING_DATE_TIME]) ? DateTimeImmutableProvider::fromString(
                $data[self::PROPERTY_PROCESSING_DATE_TIME],
                DateTimeImmutableProvider::DEFAULT_TIMEZONE
            ) : null,
            transactionResponseInfo: $data[self::PROPERTY_TRANSACTION_RESPONSE_INFO] ?? [],
            bankCode: $data[self::PROPERTY_BANK_CODE] ?? null,
            paymentSystem: $data[self::PROPERTY_PAYMENT_SYSTEM] ?? null,
            paymentServiceType: $data[self::PROPERTY_PAYMENT_SERVICE_TYPE] ?? null,
            notificationEncryption: (bool)$data[self::PROPERTY_NOTIFICATION_ENCRYPTION] ?? false,
            notificationSignature: (bool)$data[self::PROPERTY_NOTIFICATION_SIGNATURE] ?? false,
            hppOrderId: $data[self::PROPERTY_HPP_ORDER_ID] ?? '',
            processingTerminalId: $data[self::PROPERTY_PROCESSING_TERMINAL_ID] ?? null,
            processingMerchantId: $data[self::PROPERTY_PROCESSING_MERCHANT_ID] ?? null,
            creatorSystem: $data[self::PROPERTY_CREATOR_SYSTEM] ?? null,
            cardNumberMask: $data[self::PROPERTY_CARD_NUMBER_MASK] ?? null,
            desiredThreeDSMode: $data[self::PROPERTY_DESIRED_THREE_DS_MODE] ?? null,
            threeDSMode: $data[self::PROPERTY_THREE_DS_MODE] ?? null,
            acsTransId: $data[self::PROPERTY_ACS_TRANS_ID] ?? null,
            dsTransId: $data[self::PROPERTY_DS_TRANS_ID] ?? null,
            eci: $data[self::PROPERTY_ECI] ?? null,
            statusThreeDs: $data[self::PROPERTY_STATUS_THREE_DS] ?? null,
            threeDSServerTransId: $data[self::PROPERTY_THREE_DS_SERVER_TRANS_ID] ?? null,
            senderCustomerId: $data[self::PROPERTY_SENDER_CUSTOMER_ID] ?? null,
            senderFirstName: $data[self::PROPERTY_SENDER_FIRST_NAME] ?? null,
            senderLastName: $data[self::PROPERTY_SENDER_LAST_NAME] ?? null,
            senderMiddleName: $data[self::PROPERTY_SENDER_MIDDLE_NAME] ?? null,
            senderEmail: $data[self::PROPERTY_SENDER_EMAIL] ?? null,
            senderCountry: $data[self::PROPERTY_SENDER_COUNTRY] ?? null,
            senderRegion: $data[self::PROPERTY_SENDER_REGION] ?? null,
            senderCity: $data[self::PROPERTY_SENDER_CITY] ?? null,
            senderStreet: $data[self::PROPERTY_SENDER_STREET] ?? null,
            senderAdditionalAddress: $data[self::PROPERTY_SENDER_ADDITIONAL_ADDRESS] ?? null,
            senderItn: $data[self::PROPERTY_SENDER_ITN] ?? null,
            senderPassport: $data[self::PROPERTY_SENDER_PASSPORT] ?? null,
            senderIp: $data[self::PROPERTY_SENDER_IP] ?? null,
            senderPhone: $data[self::PROPERTY_SENDER_PHONE] ?? null,
            senderBirthday: $data[self::PROPERTY_SENDER_BIRTHDAY] ?? null,
            senderGender: $data[self::PROPERTY_SENDER_GENDER] ?? null,
            senderZipCode: $data[self::PROPERTY_SENDER_ZIP_CODE] ?? null,
            senderBankCode: $data[self::PROPERTY_SENDER_BANK_CODE] ?? null,
            senderPaymentSystem: $data[self::PROPERTY_SENDER_PAYMENT_SYSTEM] ?? null,
            senderCardNumberMask: $data[self::PROPERTY_SENDER_CARD_NUMBER_MASK] ?? null,
            receiptUrl: $data[self::PROPERTY_RECEIPT_URL] ?? null
        );
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRrn(): string
    {
        return $this->rrn;
    }

    /**
     * @return string|null
     */
    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    /**
     * @return int
     */
    public function getCoinAmount(): int
    {
        return $this->coinAmount;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return string
     */
    public function getOperationId(): string
    {
        return $this->operationId;
    }

    /**
     * @return string
     */
    public function getEcomOperationId(): string
    {
        return $this->ecomOperationId;
    }

    /**
     * @return string|null
     */
    public function getMerchantName(): ?string
    {
        return $this->merchantName;
    }

    /**
     * @return string|null
     */
    public function getApprovalCode(): ?string
    {
        return $this->approvalCode;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getTransactionType(): ?int
    {
        return $this->transactionType;
    }

    /**
     * @return string
     */
    public function getTransactionCurrency(): string
    {
        return $this->transactionCurrency;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreationDateTime(): ?DateTimeImmutable
    {
        return $this->creationDateTime;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getModificationDateTime(): ?DateTimeImmutable
    {
        return $this->modificationDateTime;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getProcessingDateTime(): ?DateTimeImmutable
    {
        return $this->processingDateTime;
    }

    /**
     * @return array
     */
    public function getTransactionResponseInfo(): array
    {
        return $this->transactionResponseInfo;
    }

    /**
     * @return string|null
     */
    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    /**
     * @return string|null
     */
    public function getPaymentSystem(): ?string
    {
        return $this->paymentSystem;
    }

    /**
     * @return string|null
     */
    public function getPaymentServiceType(): ?string
    {
        return $this->paymentServiceType;
    }

    /**
     * @return bool
     */
    public function isNotificationEncryption(): bool
    {
        return $this->notificationEncryption;
    }

    /**
     * @return bool
     */
    public function isNotificationSignature(): bool
    {
        return $this->notificationSignature;
    }

    /**
     * @return string
     */
    public function getHppOrderId(): string
    {
        return $this->hppOrderId;
    }

    /**
     * @return string|null
     */
    public function getProcessingTerminalId(): ?string
    {
        return $this->processingTerminalId;
    }

    /**
     * @return string|null
     */
    public function getProcessingMerchantId(): ?string
    {
        return $this->processingMerchantId;
    }

    /**
     * @return string|null
     */
    public function getCreatorSystem(): ?string
    {
        return $this->creatorSystem;
    }

    /**
     * @return string|null
     */
    public function getCardNumberMask(): ?string
    {
        return $this->cardNumberMask;
    }

    /**
     * @return string|null
     */
    public function getDesiredThreeDSMode(): ?string
    {
        return $this->desiredThreeDSMode;
    }

    /**
     * @return string|null
     */
    public function getThreeDSMode(): ?string
    {
        return $this->threeDSMode;
    }

    /**
     * @return string|null
     */
    public function getAcsTransId(): ?string
    {
        return $this->acsTransId;
    }

    /**
     * @return string|null
     */
    public function getDsTransId(): ?string
    {
        return $this->dsTransId;
    }

    /**
     * @return string|null
     */
    public function getEci(): ?string
    {
        return $this->eci;
    }

    /**
     * @return string|null
     */
    public function getStatusThreeDs(): ?string
    {
        return $this->statusThreeDs;
    }

    /**
     * @return string|null
     */
    public function getThreeDSServerTransId(): ?string
    {
        return $this->threeDSServerTransId;
    }

    /**
     * @return string|null
     */
    public function getSenderCustomerId(): ?string
    {
        return $this->senderCustomerId;
    }

    /**
     * @return string|null
     */
    public function getSenderFirstName(): ?string
    {
        return $this->senderFirstName;
    }

    /**
     * @return string|null
     */
    public function getSenderLastName(): ?string
    {
        return $this->senderLastName;
    }

    /**
     * @return string|null
     */
    public function getSenderMiddleName(): ?string
    {
        return $this->senderMiddleName;
    }

    /**
     * @return string|null
     */
    public function getSenderEmail(): ?string
    {
        return $this->senderEmail;
    }

    /**
     * @return string|null
     */
    public function getSenderCountry(): ?string
    {
        return $this->senderCountry;
    }

    /**
     * @return string|null
     */
    public function getSenderRegion(): ?string
    {
        return $this->senderRegion;
    }

    /**
     * @return string|null
     */
    public function getSenderCity(): ?string
    {
        return $this->senderCity;
    }

    /**
     * @return string|null
     */
    public function getSenderStreet(): ?string
    {
        return $this->senderStreet;
    }

    /**
     * @return string|null
     */
    public function getSenderAdditionalAddress(): ?string
    {
        return $this->senderAdditionalAddress;
    }

    /**
     * @return string|null
     */
    public function getSenderItn(): ?string
    {
        return $this->senderItn;
    }

    /**
     * @return string|null
     */
    public function getSenderPassport(): ?string
    {
        return $this->senderPassport;
    }

    /**
     * @return string|null
     */
    public function getSenderIp(): ?string
    {
        return $this->senderIp;
    }

    /**
     * @return string|null
     */
    public function getSenderPhone(): ?string
    {
        return $this->senderPhone;
    }

    /**
     * @return string|null
     */
    public function getSenderBirthday(): ?string
    {
        return $this->senderBirthday;
    }

    /**
     * @return string|null
     */
    public function getSenderGender(): ?string
    {
        return $this->senderGender;
    }

    /**
     * @return string|null
     */
    public function getSenderZipCode(): ?string
    {
        return $this->senderZipCode;
    }

    /**
     * @return string|null
     */
    public function getSenderBankCode(): ?string
    {
        return $this->senderBankCode;
    }

    /**
     * @return string|null
     */
    public function getSenderPaymentSystem(): ?string
    {
        return $this->senderPaymentSystem;
    }

    /**
     * @return string|null
     */
    public function getSenderCardNumberMask(): ?string
    {
        return $this->senderCardNumberMask;
    }

    /**
     * @return string|null
     */
    public function getReceiptUrl(): ?string
    {
        return $this->receiptUrl;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = get_object_vars($this);

        foreach ($result as $key => $value) {
            if ($value instanceof DateTimeImmutable) {
                $result[$key] = $value->format(DateTimeImmutableProvider::DEFAULT_DATE_TIME_FORMAT);
            }
        }

        return $result;
    }
}
