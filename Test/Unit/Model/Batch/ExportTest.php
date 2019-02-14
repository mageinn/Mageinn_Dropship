<?php
namespace Iredeem\Vendor\Test\Unit\Model\Batch;

class ExportTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Iredeem\Vendor\Model\Batch\Export
     */
    protected $testClassName = \Iredeem\Vendor\Model\Batch\Export::class;

    /**
     * @var \Iredeem\Vendor\Model\Batch\Export
     */
    protected $testClass;

    /**
     * Method ran before each test
     */
    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->testClass = $objectManager->getObject('Iredeem\Vendor\Model\Batch\Export');
    }

    /**
     * Test for _getFileHeading method happy flow
     * @throws \ReflectionException
     */
    public function testGetFileHeading()
    {
        // Access the method if protected
        $fileHeadings = new \ReflectionMethod($this->testClassName, '_getFileHeading');
        $fileHeadings->setAccessible(true);

        $vendorMock = $this->createPartialMock(\Iredeem\Vendor\Model\Info::class, ['getBatchExportHeadings']);
        $vendorMock->expects($this->once())
            ->method('getBatchExportHeadings')
            ->will($this->returnValue('Placeholder text for header'));

        $expectedResult = 'Placeholder text for header' . PHP_EOL;
        $result = $fileHeadings->invoke($this->testClass, $vendorMock);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getFileHeading method when the vendor does not have the header set
     * @throws \ReflectionException
     */
    public function testGetFileHeadingEmptyVendorSetting()
    {
        // Access the method if protected
        $fileHeadings = new \ReflectionMethod($this->testClassName, '_getFileHeading');
        $fileHeadings->setAccessible(true);

        $vendorMock = $this->createPartialMock(\Iredeem\Vendor\Model\Info::class, ['getBatchExportHeadings']);
        $vendorMock->expects($this->once())
            ->method('getBatchExportHeadings')
            ->will($this->returnValue(null));

        $expectedResult = null;
        $result = $fileHeadings->invoke($this->testClass, $vendorMock);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getDataForReplace method
     * @throws \ReflectionException
     */
    public function testGetDataForReplace()
    {
        // Access the method if protected
        $dataForReplace = new \ReflectionMethod($this->testClassName, '_getDataForReplace');
        $dataForReplace->setAccessible(true);
        $values = ['item.customer_name', 'po_id'];
        $dataItem = [
            'item' => [
                'customer_name' => 'Name'
            ],
            'po_id' => '000030',
            'order' => [
                'increment_id' => '8848823'
            ]
        ];

        $expectedResult = ['Name', '000030'];
        $result = $dataForReplace->invokeArgs($this->testClass, [$values, $dataItem]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getDataForReplace method with requested value not set
     * @throws \ReflectionException
     */
    public function testGetDataForReplaceRequestedValueNotSet()
    {
        // Access the method if protected
        $dataForReplace = new \ReflectionMethod($this->testClassName, '_getDataForReplace');
        $dataForReplace->setAccessible(true);
        $values = ['item.customer_name', 'billing.id', 'order_id', 'po_id', 'any_other_value.val', 'anything_else'];
        $dataItem = [
            'item' => [
                'customer_name' => 'Name'
            ],
            'po_id' => '000030',
            'order' => [
                'increment_id' => '8848823'
            ]
        ];

        $expectedResult = ['Name', '', '', '000030', '', ''];
        $result = $dataForReplace->invokeArgs($this->testClass, [$values, $dataItem]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getDataRow method
     * @throws \ReflectionException
     */
    public function testGetDataRow()
    {
        // Access the method if protected
        $dataRow = new \ReflectionMethod($this->testClassName, '_getDataRow');
        $dataRow->setAccessible(true);
        $placeholders = [
            '[item.customer_name]',
            '[billing.id]',
            '[order_id]',
            '[po_id]',
            '[any_other_value.val]',
            '[anything_else]'
        ];
        $replace = ['Name', '', '', '000030', '', ''];
        $exportFields = '[item.customer_name], [billing.id], [order_id], [po_id] [any_other_value.val], ' .
            '[anything_else] and something else';
        $isLast = false;

        $expectedResult = 'Name, , , 000030 ,  and something else' . PHP_EOL;
        $result = $dataRow->invokeArgs($this->testClass, [$placeholders, $replace, $exportFields, $isLast]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getDataRow method when the row is last
     * @throws \ReflectionException
     */
    public function testGetDataRowLastRow()
    {
        // Access the method if protected
        $dataRow = new \ReflectionMethod($this->testClassName, '_getDataRow');
        $dataRow->setAccessible(true);
        $placeholders = [
            '[item.customer_name]',
            '[billing.id]',
            '[order_id]',
            '[po_id]',
            '[any_other_value.val]',
            '[anything_else]'
        ];
        $replace = ['Name', '', '', '000030', '', ''];
        $exportFields = '[item.customer_name], [billing.id], [order_id], [po_id] [any_other_value.val], ' .
            '[anything_else] and something else';
        $isLast = true;

        $expectedResult = 'Name, , , 000030 ,  and something else';
        $result = $dataRow->invokeArgs($this->testClass, [$placeholders, $replace, $exportFields, $isLast]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getFileContents method
     *
     * Just happy flow, as the methods that are called in this methods have already been tested
     * @throws \ReflectionException
     */
    public function testGetFileContents()
    {
        $fileContents = new \ReflectionMethod($this->testClassName, '_getFileContents');
        $fileContents->setAccessible(true);
        $formattedData = [
            [
                'item' => [
                    'customer_name' => 'Name'
                ],
                'po_id' => '000030',
                'order' => [
                    'increment_id' => '8848823'
                ]
            ],
            [
                'item' => [
                    'customer_name' => 'Name2'
                ],
                'po_id' => '0000302',
                'order' => [
                    'increment_id' => '88488234'
                ]
            ],
        ];
        $placeholders = [
            '[item.customer_name]',
            '[billing.id]',
            '[order_id]',
            '[po_id]',
            '[any_other_value.val]',
            '[anything_else]'
        ];
        $values = ['item.customer_name', 'billing.id', 'order_id', 'po_id', 'any_other_value.val', 'anything_else'];
        $exportFields = '[item.customer_name], [billing.id], [order_id], [po_id] [any_other_value.val], ' .
            '[anything_else] and something else';
        $batchId = 200;

        $expectedResult = 'Name, , , 000030 ,  and something else' . PHP_EOL .
            'Name2, , , 0000302 ,  and something else';
        $result = $fileContents
            ->invokeArgs($this->testClass, [$formattedData, $placeholders, $values, $exportFields, $batchId]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getBatchRowArray method
     * @throws \ReflectionException
     */
    public function testGetBatchRowArray()
    {
        $batchRow = new \ReflectionMethod($this->testClassName, '_getBatchRowArray');
        $batchRow->setAccessible(true);

        $dataItem = [
            'item' => [
                'entity_id' => 1000,
                'sku' => 'aaa',
            ],
            'order' => [
                'increment_id' => 2000
            ],
            'po_id' => 3000,

        ];
        $batchId = 200;

        $expectedResult = [
            'batch_id' => 200,
            'order_id' => 2000,
            'shipment_id' => 3000,
            'item_id' => 1000,
            'track_id' => '',
            'order_increment_id' => 2000,
            'shipment_increment_id' => 3000,
            'item_sku' => 'aaa',
            'tracking_id' => '',
            'has_error' => 0,
            'error_info' => '',
            'row_json' => '',
        ];
        $result = $batchRow->invokeArgs($this->testClass, [$dataItem, $batchId]);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test for _getBatchRowArray method
     * @throws \ReflectionException
     */
    public function testGetBatchRowArrayMissingComponents()
    {
        $batchRow = new \ReflectionMethod($this->testClassName, '_getBatchRowArray');
        $batchRow->setAccessible(true);

        $dataItem = [
            'item' => [
                'entity_id' => 1000,
                'sku' => 'aaa',
            ],
            'order' => [
                'increment_id' => 2000
            ],

        ];
        $batchId = 200;

        $expectedResult = [
            'batch_id' => 200,
            'order_id' => 2000,
            'shipment_id' => '',
            'item_id' => 1000,
            'track_id' => '',
            'order_increment_id' => 2000,
            'shipment_increment_id' => '',
            'item_sku' => 'aaa',
            'tracking_id' => '',
            'has_error' => 0,
            'error_info' => '',
            'row_json' => '',
        ];
        $result = $batchRow->invokeArgs($this->testClass, [$dataItem, $batchId]);

        $this->assertEquals($expectedResult, $result);
    }
}
