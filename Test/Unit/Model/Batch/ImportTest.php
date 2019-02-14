<?php

namespace Mageinn\Dropship\Test\Model\Batch\Import;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Mageinn\Dropship\Model\Batch\Import;

class ImportTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Mageinn\Dropship\Model\Batch\Import
     */
    private $_import;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_csvProcessor;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment
     */
    private $_shipment;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $_order;

    /**
     * Setup function
     */
    protected function setUp()
    {
        $objectManager = new ObjectManagerHelper($this);

        $this->_csvProcessor = $this->createMock(\Magento\Framework\File\Csv::class);
        $this->_shipment = $this->createMock(\Magento\Sales\Model\Order\Shipment::class);
        $this->_order = $this->createMock(\Magento\Sales\Model\Order::class);

        $this->_import = $objectManager->getObject(
            Import::class,
            [
                'csvProcessor' => $this->_csvProcessor,
            ]
        );

        $order = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
            ->setIncrementId('000000001');
        $this->_shipment->expects($this->any())->method('hasData')->willReturn(true);
        $this->_shipment->expects($this->any())->method('getId')->willReturn(1);
        $this->_shipment->expects($this->any())->method('getOrderId')->willReturn(1);
        $this->_shipment->expects($this->any())->method('getIncrementId')->willReturn(1);
        $this->_shipment->expects($this->any())->method('getOrder')->willReturn($order);
    }

    // @codingStandardsIgnoreStart
    /**
     * Test iport function
     */
    public function testImportData()
    {
        $source = $absolutePath = dirname(dirname(__DIR__)) . '/_files/import/dummy.csv';
        $template = ['order_id', 'tracking_id', 'carrier'];

        $this->_csvProcessor
            ->expects($this->once())
            ->method('getData')
            ->with($source)
            ->willReturn([
                [1,2,3]
            ]);
        $expected = [[
            'order_id' => 1,
            'tracking_id' => 2,
            'carrier' => 3,
        ]];

        $actual = $this->_import->getImportData($source, $template, ',');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testMissingFileImportData()
    {
        $source = $absolutePath = dirname(dirname(__DIR__)) . '/_files/import/missing.csv';
        $template = ['order_id', 'tracking_id', 'carrier'];

        $actual = $this->_import->getImportData($source, $template, ',');

        $this->assertEquals(false, $actual);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testThrowExceptionImportData()
    {
        $source = $absolutePath = dirname(dirname(__DIR__)) . '/_files/import/dummy.csv';
        $template = ['order_id', 'tracking_id', 'carrier'];

        $this->_csvProcessor
            ->expects($this->once())
            ->method('getData')
            ->with($source)
            ->willThrowException(new \Exception(__('File "' . $source . '" does not exist')));

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('File "' . $source . '" does not exist');
        $this->_import->getImportData($source, $template, ',');
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testRemoveHeaderImportData()
    {
        $source = $absolutePath = dirname(dirname(__DIR__)) . '/_files/import/dummy.csv';
        $template = ['order_id', 'tracking_id', 'carrier'];

        $this->_csvProcessor
            ->expects($this->once())
            ->method('getData')
            ->with($source)
            ->willReturn([
                ['order_id', 'tracking_id', 'carrier'],
                [1,2,3]
            ]);
        $expected = [[
            'order_id' => 1,
            'tracking_id' => 2,
            'carrier' => 3,
        ]];

        $actual = $this->_import->getImportData($source, $template, ',');

        $this->assertEquals($expected, $actual);
    }

    public function testInvalidImportData()
    {
        $source = $absolutePath = dirname(dirname(__DIR__)) . '/_files/import/dummy.csv';
        $template = ['order_id', 'tracking_id', 'carrier'];

        $this->_csvProcessor
            ->expects($this->once())
            ->method('getData')
            ->with($source)
            ->willReturn([
                ['order_id','po_id'],
                [1,2,3]
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid file row format');
        $this->_import->getImportData($source, $template, ',');
    }

    public function testTemplateValidation()
    {
        $template = '[order_id],[tracking_id],[carrier]';
        $expected = ['order_id', 'tracking_id', 'carrier'];

        $valid = $this->_import->validateImportTemplate($template, ',');
        $this->assertEquals($expected, $valid);
    }

    public function testInvalidTemplateValidation()
    {
        $template = 'order_id,tracking_id,carrier';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid import template');
        $this->_import->validateImportTemplate($template, ',');
    }

    public function testMissingTemplateValidation()
    {
        $template = '[tracking_id],[carrier]';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing required field');
        $this->_import->validateImportTemplate($template, ',');
    }

    public function testEitherTemplateValidation()
    {
        $template = '[order_id],[po_id],[tracking_id],[carrier]';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Either po_id OR order_id can be specified, but not both');
        $this->_import->validateImportTemplate($template, ',');
    }

    public function testInvalidDelimiterTemplateValidation()
    {
        $template = '[order_id];[tracking_id];[carrier]';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Template contains invalid delimiter');
        $this->_import->validateImportTemplate($template, ',');
    }

    public function testMinimumTrackData()
    {
        $trackData = [
            'tracking_id' => '123ABC'
        ];
        $expected = [
            'number' => "123ABC",
            'carrier_code' => "",
            'title' => ""
        ];

        $track = new \ReflectionMethod(Import::class, '_getTrack');
        $track->setAccessible(true);
        $actual = $track->invoke($this->_import, $trackData);

        $this->assertEquals($expected, $actual);
    }

    public function testAllTrackData()
    {
        $trackData = [
            'dummy' => '123ABC'
        ];

        $track = new \ReflectionMethod(Import::class, '_getTrack');
        $track->setAccessible(true);
        $actual = $track->invoke($this->_import, $trackData);

        $this->assertEquals(null, $actual);
    }

    public function testMissingTrackData()
    {
        $trackData = [
            'tracking_id' => '123ABC',
            'carrier' => 'dhl',
            'title' => 'DHL',
        ];
        $expected = [
            'number' => "123ABC",
            'carrier_code' => "dhl",
            'title' => "DHL"
        ];

        $track = new \ReflectionMethod(Import::class, '_getTrack');
        $track->setAccessible(true);
        $actual = $track->invoke($this->_import, $trackData);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \ReflectionException
     */
    public function testBatchRowArray()
    {
        $trackData = [
            'tracking_id' => '123ABC',
            'carrier' => 'dhl',
            'title' => 'DHL',
        ];
        $expected = [
            'batch_id' => 1,
            'order_id' => null,
            'shipment_id' => null,
            'order_increment_id' => '',
            'shipment_increment_id' => '',
            'track_id' => '123ABC',
            'has_error' => 1,
            'error_info' => 'Error',
        ];

        $track = new \ReflectionMethod(Import::class, '_getBatchRowArray');
        $track->setAccessible(true);
        $actual = $track->invoke($this->_import, 1, $trackData, null, 'Error');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \ReflectionException
     */
    public function testShipmentBatchRowArray()
    {
        $trackData = [
            'number' => '123ABC',
            'carrier' => 'dhl',
            'title' => 'DHL',
        ];
        $expected = [
            'batch_id' => 1,
            'order_id' => 1,
            'shipment_id' => 1,
            'order_increment_id' => '000000001',
            'shipment_increment_id' => 1,
            'track_id' => '123ABC',
            'has_error' => 0,
            'error_info' => '',
        ];

        $track = new \ReflectionMethod(Import::class, '_getBatchRowArray');
        $track->setAccessible(true);
        $actual = $track->invoke($this->_import, 1, $trackData, $this->_shipment);

        $this->assertEquals($expected, $actual);
    }

    // @codingStandardsIgnoreEnd
}
