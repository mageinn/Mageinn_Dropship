<?php
namespace Mageinn\Dropship\Test\Unit\Helper;

use \Mageinn\Dropship\Helper\ShippingRates as TestClass;
use \PHPUnit\Framework\TestCase;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Framework\Filesystem\Io\File;

class ShippingRatesTest extends TestCase
{
    /**
     * @var string
     */
    protected $testClassName = TestClass::class;

    /**
     * @var TestClass
     */
    protected $testClass;

    /**
     * Method ran before each test
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $file = $objectManager->getObject(File::class);
        $this->testClass = $objectManager->getObject(
            $this->testClassName,
            [
                'regionsArray' => ['EU Europe', 'Europe'],
                'countriesArray' => [
                    'Romania' => 'RO',
                    'United Kingdom' => 'GB'
                ],
                'fileSystem' => $file,
            ]
        );
    }

    /**
     * Test for setInsertRows method
     */
    public function testSetInsertRows()
    {
        $insertRow = ['test' => ['key1' => 'value1', 'key2' => 'value2']];

        $expectedResult = ['test' => ['key1' => 'value1', 'key2' => 'value2']];
        $result = $this->testClass->setInsertRows($insertRow)->getInsertRows();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for addInsertRow method
     */
    public function testAddInsertRow()
    {
        $key = 'key1';
        $value = ['value' => 'abc', 'another value' => 'xyz'];

        $expectedResult = ['key1' => ['value' => 'abc', 'another value' => 'xyz']];
        $this->testClass->addInsertRow($key, $value);
        $result = $this->testClass->getInsertRows();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for clearInsertRows method
     */
    public function testClearInsertRows()
    {
        $key = 'key1';
        $value = ['value' => 'abc', 'another value' => 'xyz'];

        $expectedResultInsertRow = ['key1' => ['value' => 'abc', 'another value' => 'xyz']];
        $this->testClass->addInsertRow($key, $value);
        $resultInsertRow = $this->testClass->getInsertRows();
        $this->assertEquals($expectedResultInsertRow, $resultInsertRow);

        $expectedResult = [];
        $this->testClass->clearInsertRows();
        $result = $this->testClass->getInsertRows();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for createInsertRow method
     */
    public function testCreateInsertRow()
    {
        $vendorId = 1;
        $country = 'Romania';
        $shippingGroup = 'Small';
        $deliveryTime = 15;
        $price = 20;

        $vendorId2 = 1;
        $country2 = 'Romania';
        $shippingGroup2 = 'Large';
        $deliveryTime2 = 30;
        $price2 = 25;

        $expectedResult = [
            'Romania_Small' => [
                'vendor_id' => 1,
                'country' => 'Romania',
                'shipping_group' => 'Small',
                'delivery_time' => 15,
                'price' => 20
            ],
            'Romania_Large' => [
                'vendor_id' => 1,
                'country' => 'Romania',
                'shipping_group' => 'Large',
                'delivery_time' => 30,
                'price' => 25
            ],
        ];
        $this->testClass->createInsertRow($vendorId, $country, $shippingGroup, $deliveryTime, $price);
        $this->testClass->createInsertRow($vendorId2, $country2, $shippingGroup2, $deliveryTime2, $price2);
        $result = $this->testClass->getInsertRows();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for setErrorMessages method
     */
    public function testSetErrorMessages()
    {
        $insertMessage = ['insert message', 'another one'];

        $expectedResult = ['insert message', 'another one'];
        $result = $this->testClass->setErrorMessages($insertMessage)->getErrorMessages();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for addErrorMessage method
     */
    public function testAddErrorMessage()
    {
        $message = 'error message';
        $anotherMessage = 'another message';

        $expectedResult = ['error message', 'another message'];
        $this->testClass->addErrorMessage($message);
        $this->testClass->addErrorMessage($anotherMessage);
        $result = $this->testClass->getErrorMessages();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for clearErrorMessages method
     */
    public function testClearErrorMessages()
    {
        $message = 'message';

        $expectedResultInsertMessage = ['message'];
        $this->testClass->addErrorMessage($message);
        $resultInsertMessage = $this->testClass->getErrorMessages();
        $this->assertEquals($expectedResultInsertMessage, $resultInsertMessage);

        $expectedResult = [];
        $this->testClass->clearErrorMessages();
        $result = $this->testClass->getErrorMessages();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for checkRegion
     */
    public function testCheckRegion()
    {
        $region = 'Europe';
        $row = 10;

        $result = $this->testClass->checkRegion($region, $row);

        $this->assertTrue($result);
    }

    /**
     * Test for checkRegion
     */
    public function testCheckRegionWithError()
    {
        $region = 'Asia';
        $row = 10;

        $errorMessage = 'Region Asia declared on row #10 is undefined in the system';
        $result = $this->testClass->checkRegion($region, $row);
        $errorMessages = $this->testClass->getErrorMessages();

        $this->assertFalse($result);
        $this->assertContains($errorMessage, $errorMessages);
    }

    /**
     * Test for checkCountry
     */
    public function testCheckCountry()
    {
        $country = 'Romania';
        $row = 10;

        $result = $this->testClass->checkCountry($country, $row);

        $this->assertTrue($result);
    }

    /**
     * Test for checkCountry
     */
    public function testCheckCountryWithError()
    {
        $country = 'Estonia';
        $row = 10;

        $errorMessage = 'Country Estonia declared on row #10 is undefined in the system';
        $result = $this->testClass->checkCountry($country, $row);
        $errorMessages = $this->testClass->getErrorMessages();

        $this->assertFalse($result);
        $this->assertContains($errorMessage, $errorMessages);
    }

    /**
     * Test for checkIsNumber
     */
    public function testCheckIsNumber()
    {
        $value = '23';
        $row = 20;

        $result = $this->testClass->checkIsNumber($value, $row);

        $this->assertTrue($result);
    }

    /**
     * Test for checkIsNumber
     */
    public function testCheckIsNumberWithError()
    {
        $value = '23a';
        $row = 20;

        $errorMessage = 'Value 23a declared on row #20 is not a number';
        $result = $this->testClass->checkIsNumber($value, $row);
        $errorMessages = $this->testClass->getErrorMessages();

        $this->assertFalse($result);
        $this->assertContains($errorMessage, $errorMessages);
    }

    /**
     * Test for isCsv method
     */
    public function testIsCsv()
    {
        $file = 'test.file.csv';

        $result = $this->testClass->isCsv($file);

        $this->assertTrue($result);
    }

    /**
     * Test for isCsv method
     */
    public function testIsCsvFalse()
    {
        $file = 'test.file.txt';

        $result = $this->testClass->isCsv($file);

        $this->assertFalse($result);
    }
}
