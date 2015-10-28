<?php

namespace JUIT\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use JUIT\Doctrine\DBAL\Types\PhoneNumberType;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var PhoneNumberType */
    private $SUT;

    /** @var MySqlPlatform */
    private $platform;

    public static function setUpBeforeClass()
    {
        Type::addType('phone_number', PhoneNumberType::class);
    }

    protected function setUp()
    {
        $this->platform = new MySqlPlatform();
        $this->SUT = Type::getType('phone_number');
    }

    /** @test */
    public function it_converts_a_phone_number_instance_to_the_appropriate_database_value()
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneNumberUtil->parse('0123456789', 'DE');

        $actual = $this->SUT->convertToDatabaseValue($phoneNumber, $this->platform);

        $this->assertEquals('+49123456789', $actual);
    }

    /** @test */
    public function it_converts_a_database_value_to_the_appropriate_phone_number_instance()
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $phoneNumber = $this->SUT->convertToPHPValue('+49123456789', $this->platform);

        $this->assertEquals('+49123456789', $phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164));
    }

    /** @test */
    public function it_converts_php_null_to_database_null()
    {
        $this->assertNull($this->SUT->convertToDatabaseValue(null, $this->platform));
    }

    /** @test */
    public function it_converts_database_null_to_php_null()
    {
        $this->assertNull($this->SUT->convertToPHPValue(null, $this->platform));
    }

    /** @test */
    public function it_throws_an_exception_on_invalid_input_from_database()
    {
        $this->setExpectedException(ConversionException::class);
        $this->SUT->convertToPHPValue('abcde', $this->platform);
    }

    /** @test */
    public function it_throws_an_exception_on_invalid_input_from_php()
    {
        $this->setExpectedException(ConversionException::class);
        $this->SUT->convertToDatabaseValue('abcde', $this->platform);
    }
}
