<?php

namespace JUIT\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberType extends StringType
{
    /**
     * @param null|PhoneNumber $value
     * @param AbstractPlatform $platform
     * @return PhoneNumber|null
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }
        if (!$value instanceof PhoneNumber) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return PhoneNumberUtil::getInstance()->format($value, PhoneNumberFormat::E164);
    }

    /**
     * @param null|string $value
     * @param AbstractPlatform $platform
     * @return PhoneNumber|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        try {
            $phoneNumber = PhoneNumberUtil::getInstance()->parse($value, 'DE');
        } catch (NumberParseException $e) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $phoneNumber;
    }
}
