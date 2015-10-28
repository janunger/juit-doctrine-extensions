<?php

namespace JUIT\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use JUIT\Doctrine\DBAL\Types\UtcDateTimeType;

class UtcDateTimeTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $platform;

    /**
     * @var UtcDateTimeType
     */
    protected $type;

    public static function setUpBeforeClass()
    {
        Type::addType("utcdatetime", "JUIT\Doctrine\DBAL\Types\UtcDateTimeType");
    }

    protected function setUp()
    {
        $this->type = Type::getType("utcdatetime");
        $this->platform = new MySqlPlatform();
    }

    public function testDateTimeConvertsToNormalizedDatabaseValue()
    {
        $date = new \DateTime("1973-05-12 00:00:00", new \DateTimeZone("Europe/Berlin"));

        $expected = "1973-05-11 23:00:00";
        $actual = $this->type->convertToDatabaseValue($date, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    public function testDateTimeWithDaylightSavingsTimeInEffectConvertsToNormalizedDatabaseValue()
    {
        $date = new \DateTime("2013-05-12 00:00:00", new \DateTimeZone("Europe/Berlin"));

        $expected = "2013-05-11 22:00:00";
        $actual = $this->type->convertToDatabaseValue($date, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    public function testNormalizedDatabaseValueConvertsToPHPValueWithDefaultDateTimeZone()
    {
        $actual = $this->type->convertToPHPValue("2013-05-11 22:00:00", $this->platform);

        $this->assertInstanceOf("\DateTime", $actual);
        $this->assertEquals(date_default_timezone_get(), $actual->getTimezone()->getName());

        $expected = new \DateTime("2013-05-11 22:00:00", new \DateTimeZone("UTC"));
        $expected->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        $this->assertEquals($expected, $actual);
    }

    public function testNullConvertsToPHPValueNull()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testNullConvertsToDatabaseValueNull()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testThrowsExceptionOnInvalidDatabaseValue()
    {
        $this->type->convertToPHPValue("abcde", $this->platform);
    }
}
