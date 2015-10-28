<?php

namespace JUIT\Tests\Doctrine\DBAL\Types;

use DateTime;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use JUIT\Doctrine\DBAL\Types\UtcDateTimeType;

class UtcDateTimeTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var MySqlPlatform */
    protected $platform;

    /** @var UtcDateTimeType */
    protected $type;

    public static function setUpBeforeClass()
    {
        Type::addType('utcdatetime', UtcDateTimeType::class);
    }

    protected function setUp()
    {
        $this->platform = new MySqlPlatform();
        $this->type = Type::getType('utcdatetime');
    }

    /** @test */
    public function it_converts_a_given_date_time_instance_to_a_normalized_database_value()
    {
        $date = new DateTime('1973-05-12 00:00:00', new \DateTimeZone('Europe/Berlin'));

        $expected = '1973-05-11 23:00:00';
        $actual = $this->type->convertToDatabaseValue($date, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_converts_a_given_date_time_instance_with_daylight_savings_time_in_effect_to_a_normalized_database_value()
    {
        $date = new DateTime('2013-05-12 00:00:00', new \DateTimeZone('Europe/Berlin'));

        $expected = '2013-05-11 22:00:00';
        $actual = $this->type->convertToDatabaseValue($date, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_converts_a_database_value_to_a_date_time_instance_with_default_date_time_zone()
    {
        $actual = $this->type->convertToPHPValue('2013-05-11 22:00:00', $this->platform);

        $this->assertInstanceOf(DateTime::class, $actual);
        $this->assertEquals(date_default_timezone_get(), $actual->getTimezone()->getName());

        $expected = new DateTime('2013-05-11 22:00:00', new \DateTimeZone('UTC'));
        $expected->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_converts_database_null_to_php_null()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    /** @test */
    public function it_converts_php_null_to_database_null()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    /** @test */
    public function it_throws_an_exception_on_invalid_input_from_database()
    {
        $this->setExpectedException(ConversionException::class);
        $this->type->convertToPHPValue('abcde', $this->platform);
    }

    /** @test */
    public function it_throws_an_exception_on_invalid_input_from_php()
    {
        $this->setExpectedException(ConversionException::class);
        $this->type->convertToDatabaseValue('abcde', $this->platform);
    }
}
