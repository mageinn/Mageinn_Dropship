<?php
namespace Mageinn\Dropship\Test\Unit\Model\Batch\Handler;

/**
 * Class TransferTest
 * @package Mageinn\Dropship\Test\Unit\Model\Batch\Handler
 */
class TransferTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Mageinn\Dropship\Model\Batch\Handler\Transfer
     */
    protected $testClassName = \Mageinn\Dropship\Model\Batch\Handler\Transfer::class;

    /**
     * @var \Mageinn\Dropship\Model\Batch\Handler\Transfer
     */
    protected $testClass;

    /**
     * Method ran before each test
     */
    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->testClass = $objectManager->getObject($this->testClassName);
    }

    public function testIsSftp()
    {
        $sftp = 'sftp://username@endpoint/file.csv';

        $expectedResult = [
            'sftp://username@endpoint/file.csv',
            'username',
            'endpoint',
            'file.csv'
        ];

        $result = $this->testClass->isSftp($sftp);

        $this->assertEquals($expectedResult, $result);
    }

    public function testIsNotSftp()
    {
        $sftp = 'http://username@endpoint/file.csv';

        $result = $this->testClass->isSftp($sftp);

        $this->assertEquals(false, $result);
    }

    public function testIsMissingUsernameSftp()
    {
        $sftp = 'sftp://endpoint/file.csv';

        $result = $this->testClass->isSftp($sftp);

        $this->assertEquals(false, $result);
    }

    public function testIsMissingEndpointSftp()
    {
        $sftp = 'sftp://username@/file.csv';

        $result = $this->testClass->isSftp($sftp);

        $this->assertEquals(false, $result);
    }

    public function testIsMissingPathSftp()
    {
        $sftp = 'sftp://username@endpoint/';

        $result = $this->testClass->isSftp($sftp);

        $this->assertEquals(false, $result);
    }
}
