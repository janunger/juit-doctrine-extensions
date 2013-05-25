<?php

namespace JUIT\Doctrine\DBAL\Types;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\Type;

class UtcDateTimeType extends DateTimeType
{
    /**
     * @var null|DateTimeZone
     */
    private static $utcDateTimeZone = null;

    /**
     * @var null|DateTimeZone
     */
    private static $defaultDateTimeZone = null;

    /**
     * @return DateTimeZone
     */
    private static function getUTCDateTimeZone()
    {
        if (null === self::$utcDateTimeZone) {
            self::$utcDateTimeZone = new DateTimeZone("UTC");
        }
        return self::$utcDateTimeZone;
    }

    /**
     * @return DateTimeZone
     */
    private static function  getDefaultDateTimeZone()
    {
        if (null === self::$defaultDateTimeZone) {
            self::$defaultDateTimeZone = new DateTimeZone(date_default_timezone_get());
        }
        return self::$defaultDateTimeZone;
    }

    /**
     * @param DateTime $value
     * @param AbstractPlatform $platform
     * @return null|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $value->setTimezone(new DateTimeZone('UTC'));
        return $value->format($platform->getDateTimeFormatString());
    }

    /**
     * @param null|string $value
     * @param AbstractPlatform $platform
     * @return DateTime|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $val = DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::getUTCDateTimeZone()
        );
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        $val->setTimezone(self::getDefaultDateTimeZone());

        return $val;
    }
}
