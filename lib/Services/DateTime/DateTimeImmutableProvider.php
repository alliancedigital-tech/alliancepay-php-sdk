<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\DateTime;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

/**
 * Class DateTimeImmutableProvider.
 */
class DateTimeImmutableProvider
{
    public const DEFAULT_DATE_TIME_FORMAT = 'Y-m-d H:i:s.vP';
    public const DEFAULT_TIMEZONE = 'UTC';
    public const KYIV_TIMEZONE = 'Europe/Kyiv';

    /**
     * @throws Exception
     */
    public static function nowByTimezone(string $tz): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone($tz));
    }

    public function defaultDateUtc(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            '01-01-1970 00:00:00',
            new DateTimeZone(self::DEFAULT_TIMEZONE)
        );
    }

    /**
     * @throws Exception
     */
    public static function fromString(string $date, string $tz): DateTimeImmutable
    {
        return new DateTimeImmutable(
            self::normalizeDate($date),
            new DateTimeZone($tz)
        );
    }

    /**
     * @param string|null $dateString
     * @return string|null
     */
    protected static function normalizeDate(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            $dateString = trim((string)$dateString);

            if (preg_match('/^(\d{4})\.(\d{2})\.(\d{2})/', $dateString, $matches)) {
                $datePart = "{$matches[1]}-{$matches[2]}-{$matches[3]}";
                $timePart = substr($dateString, 10);
                $normalized = $datePart . $timePart;
            } else {
                $normalized = $dateString;
            }

            if (preg_match('/ (\+|\-)\d{4}$/', $normalized)) {
                $normalized = preg_replace('/ (\+|\-)(\d{4})$/', '$1$2', $normalized);
            }

            return $normalized;

        } catch (Exception $e) {
            return null;
        }
    }
}
