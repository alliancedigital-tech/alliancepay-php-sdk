<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace AlliancePay\Sdk\Services\DateTime;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use InvalidArgumentException;

/**
 * Class DateTimeImmutableProvider.
 */
final class DateTimeImmutableProvider
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

    /**
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function defaultDateUtc(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            '01-01-1970 00:00:00',
            new DateTimeZone(self::DEFAULT_TIMEZONE)
        );
    }

    /**
     * @param string|null $date
     * @param string $tz
     * @return DateTimeImmutable
     * @throws InvalidArgumentException|Exception
     */
    public static function fromString(?string $date, string $tz): DateTimeImmutable
    {
        $normalized = self::normalizeDate($date);

        if ($normalized === null) {
            throw new InvalidArgumentException("Date string cannot be empty or null.");
        }

        return new DateTimeImmutable($normalized, new DateTimeZone($tz));
    }

    /**
     * @param string|null $dateString
     * @return string|null
     */
    protected static function normalizeDate(?string $dateString): ?string
    {
        if ($dateString === null || trim($dateString) === '') {
            return null;
        }

        $normalized = trim($dateString);

        if (preg_match('/^(\d{4})\.(\d{2})\.(\d{2})/', $normalized, $matches)) {
            $datePart = "{$matches[1]}-{$matches[2]}-{$matches[3]}";
            $timePart = (strlen($normalized) > 10) ? substr($normalized, 10) : '';
            $normalized = $datePart . $timePart;
        }

        if (preg_match('/ (\+|\-)\d{4}$/', $normalized)) {
            $normalized = preg_replace('/ (\+|\-)(\d{4})$/', '$1$2', $normalized);
        }

        return $normalized;
    }
}
