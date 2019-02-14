<?php

namespace Iredeem\Vendor\Test\Model\Source;

use Iredeem\Vendor\Model\Source\ShipmentStatus;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use ReflectionClass;

class ShipmentStatusTest extends \PHPUnit\Framework\TestCase
{
    /** @var ShipmentStatus */
    protected $sourceData;

    protected function setUp()
    {
        $objectManager = new ObjectManagerHelper($this);

        $this->sourceData = $objectManager->getObject(ShipmentStatus::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLabelValue()
    {
        $statusLabel = ShipmentStatus::getLabel(ShipmentStatus::SHIPMENT_STATUS_SHIPPED);
        
        $reflection = new ReflectionClass($this->sourceData);
        $staticProperties = $reflection->getStaticProperties();
        $statusOptions = $staticProperties['statusOptions'];

        $this->assertEquals(
            $statusOptions[ShipmentStatus::SHIPMENT_STATUS_SHIPPED],
            $statusLabel
        );
    }

    public function testOptionArray()
    {
        $options = $this->sourceData->toOptionArray();
        $option = array_pop($options);

        $this->assertArrayHasKey('value', $option);
        $this->assertArrayHasKey('label', $option);
    }
}
