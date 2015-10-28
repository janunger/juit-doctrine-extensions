<?php

namespace JUIT\Doctrine\DBAL\Types;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UtcDateTimeType extends DateTimeType
{
    /** @var null|DateTimeZone */
    private static $utcDateTimeZone = null;

    /** @var null|DateTimeZone */
    private static $defaultDateTimeZone = null;

    /** @return DateTimeZone */
    private static function getUTCDateTimeZone()
    {
        if (null === self::$utcDateTimeZone) {
            self::$utcDateTimeZone = new DateTimeZone("UTC");
        }
        return self::$utcDateTimeZone;
    }

    /** @return DateTimeZone */
    private static function  getDefaultDateTimeZone()
    {
        if (null === self::$defaultDateTimeZone) {
            self::$defaultDateTimeZone = new DateTimeZone(date_default_timezone_get());
        }
        return self::$defaultDateTimeZone;
    }

    /**
     * @param DateTimeImmutable $value
     * @param AbstractPlatform $platform
     * @return null|string
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        if (!$value instanceof DateTimeImmutable) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        $value = $value->setTimezone(self::getUTCDateTimeZone());

        return $value->format($platform->getDateTimeFormatString());
    }

    /**
     * @param null|string $value
     * @param AbstractPlatform $platform
     * @return DateTimeImmutable|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $val = DateTimeImmutable::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::getUTCDateTimeZone()
        );
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        $val = $val->setTimezone(self::getDefaultDateTimeZone());

        return $val;
    }
}
