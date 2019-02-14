<?php
namespace Iredeem\Vendor\Test\Unit\Model\Batch\Export;

/**
 * Class ResponseTest
 * @package Iredeem\Vendor\Test\Unit\Model\Batch\Export
 */
class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $_testClassName = \Iredeem\Vendor\Model\Batch\Export\Response::class;

    /**
     * @var \Iredeem\Vendor\Model\Batch\Export\Response
     */
    protected $_testClass;

    /**
     * Method ran before each test
     */
    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_testClass = $objectManager->getObject('Iredeem\Vendor\Model\Batch\Export\Response');
    }

    /**
     * Test for setNotes method
     */
    public function testSetNotes()
    {
        $notes = [
            'note1',
            'note2',
            'note3'
        ];

        $expectedResult = 'note1' . PHP_EOL . 'note2' . PHP_EOL . 'note3';
        $result = $this->_testClass->setNotes($notes)->getNotes();

        $this->assertEquals($expectedResult, $result);
    }
}
