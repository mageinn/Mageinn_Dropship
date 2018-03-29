<?php
namespace Mageinn\Dropship\Ui\DataProviders\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;

/**
 * Data provider for "Custom Attribute" field of product page
 */
class DeliveryCountry extends AbstractModifier
{

    const DELIVERY_COUNTRY_ATTR_CODE = 'delivery_country';
    const DELIVERY_COUNTRY_OVERRIDE_ATTR_CODE = 'delivery_country_override';

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface     $urlBuilder
     * @param ArrayManager     $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->customiseDeliveryCountryAttrField($meta);

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Customise Custom Attribute field
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customiseDeliveryCountryAttrField(array $meta)
    {
        $path = $this->arrayManager->findPath(
            self::DELIVERY_COUNTRY_ATTR_CODE,
            $meta,
            null,
            'children'
        );
        $meta = $this->arrayManager->merge($path . static::META_CONFIG_PATH, $meta, [
            'imports' => [
                'disabled' => '!ns = ${ $.ns }, index = ' . self::DELIVERY_COUNTRY_OVERRIDE_ATTR_CODE . ':checked'
            ],
        ]);

        return $meta;
    }
}
