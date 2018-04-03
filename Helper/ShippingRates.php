<?php

namespace Mageinn\Vendor\Helper;

use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Helper\Context;
use \Mageinn\Vendor\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use \Magento\Framework\Filesystem\Io\File;

/**
 * Class ShippingRates
 *
 * General helper for Vendor module
 *
 * @package Mageinn\Vendor\Helper
 */
class ShippingRates extends Data
{
    const REGION_ARRAY_POSITION = 0;
    const COUNTRY_ARRAY_POSITION = 1;
    const SHIPPING_GROUP_ARRAY_POSITION = 2;
    const DELIVERY_TIME_ARRAY_POSITION = 3;
    const PRICE_ARRAY_POSITION = 4;
    const HEADINGS_ROW_NUMBER = 0;

    /**
     * @var RegionCollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @var array
     */
    protected $insertRows;

    /**
     * @var array
     */
    protected $errorMessages;

    /**
     * @var array
     */
    protected $regionsArray;

    /**
     * @var array
     */
    protected $countriesArray;

    /**
     * @var File
     */
    protected $fileSystem;

    /**
     * @var array
     */
    protected $allowedFileExtensions = ['csv'];

    /**
     * ShippingRates constructor.
     * @param RegionCollectionFactory $collectionFactory
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param File $file
     */
    public function __construct(
        RegionCollectionFactory $collectionFactory,
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        File $file
    ) {
        $this->regionCollectionFactory = $collectionFactory;
        $this->fileSystem = $file;
        $this->regionsArray = [];
        $this->countriesArray = [];
        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return array
     */
    public function getInsertRows()
    {
        return $this->insertRows;
    }

    /**
     * @param array $insertRows
     * @return ShippingRates
     */
    public function setInsertRows($insertRows)
    {
        $this->insertRows = $insertRows;
        return $this;
    }

    /**
     * @param string $key
     * @param array $value
     */
    public function addInsertRow($key, $value)
    {
        $this->insertRows[$key] = $value;
    }

    /**
     * Clear the insert rows
     */
    public function clearInsertRows()
    {
        $this->insertRows = [];
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @param array $errorMessages
     * @return ShippingRates
     */
    public function setErrorMessages($errorMessages)
    {
        $this->errorMessages = $errorMessages;
        return $this;
    }

    /**
     * @param string $message
     */
    public function addErrorMessage($message)
    {
        $this->errorMessages[] = $message;
    }

    /**
     * Clear the error messages
     */
    public function clearErrorMessages()
    {
        $this->errorMessages = [];
    }

    /**
     * Process the data received in the CSV file for the vendor
     *
     * @param $vendorId
     * @param $csvContent
     * @return bool
     */
    public function process($vendorId, $csvContent)
    {
        $result = true;
        $this->clearInsertRows();
        $this->clearErrorMessages();
        $this->setCountriesArray();
        $this->setRegionsArray();
        foreach ($csvContent as $row => $shippingRate) {
            // Skip checking the headings row
            if ($row == self::HEADINGS_ROW_NUMBER) {
                continue;
            }
            // Increase the row number so that we can have row starting from 1 instead on 0
            $row++;
            if ($shippingRate[self::REGION_ARRAY_POSITION] && !$shippingRate[self::COUNTRY_ARRAY_POSITION]) {
                // If the region is set, do the checks and create insert
                // rows based on region for all countries in that region
                if ($this->checkRegion($shippingRate[self::REGION_ARRAY_POSITION], $row)
                    && $this->checkIsNumber($shippingRate[self::DELIVERY_TIME_ARRAY_POSITION], $row)
                    && $this->checkIsNumber($shippingRate[self::PRICE_ARRAY_POSITION], $row)
                ) {
                    $this->createInsertRowForRegion(
                        $shippingRate[self::REGION_ARRAY_POSITION],
                        $vendorId,
                        $shippingRate[self::SHIPPING_GROUP_ARRAY_POSITION],
                        $shippingRate[self::DELIVERY_TIME_ARRAY_POSITION],
                        $shippingRate[self::PRICE_ARRAY_POSITION]
                    );
                } else {
                    $result = false;
                }
            } elseif ($shippingRate[self::COUNTRY_ARRAY_POSITION] && !$shippingRate[self::REGION_ARRAY_POSITION]) {
                // If the country is set, perform checks and create insert row for the country
                if ($this->checkCountry($shippingRate[self::COUNTRY_ARRAY_POSITION], $row)
                    && $this->checkIsNumber($shippingRate[self::DELIVERY_TIME_ARRAY_POSITION], $row)
                    && $this->checkIsNumber($shippingRate[self::PRICE_ARRAY_POSITION], $row)
                ) {
                    $this->createInsertRow(
                        $vendorId,
                        $this->countriesArray[$shippingRate[self::COUNTRY_ARRAY_POSITION]],
                        $shippingRate[self::SHIPPING_GROUP_ARRAY_POSITION],
                        $shippingRate[self::DELIVERY_TIME_ARRAY_POSITION],
                        $shippingRate[self::PRICE_ARRAY_POSITION]
                    );
                } else {
                    $result = false;
                }
            } else {
                // If both region and country are set for a row, add error message
                $this->addErrorMessage(__('Row #%1 is invalid: it has both region and country', $row));
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Creates insert row for bulk insert, a shipping rate row in the shipping rates table
     *
     * @param $vendorId
     * @param $country
     * @param $shippingGroup
     * @param $deliveryTime
     * @param $price
     */
    public function createInsertRow($vendorId, $country, $shippingGroup, $deliveryTime, $price)
    {
        $key = $country . '_' . $shippingGroup;
        $value = [
            'vendor_id' => $vendorId,
            'country' => $country,
            'shipping_group' => $shippingGroup,
            'delivery_time' => $deliveryTime,
            'price' => $price,
        ];

        $this->addInsertRow($key, $value);
    }

    /**
     * Creates insert rows for a country in a region
     *
     * @param $region
     * @param $vendorId
     * @param $shippingGroup
     * @param $deliveryTime
     * @param $price
     */
    public function createInsertRowForRegion($region, $vendorId, $shippingGroup, $deliveryTime, $price)
    {
        $countries = $this->getCountriesForRegion($region);

        foreach ($countries as $country) {
            $this->createInsertRow($vendorId, $country->getCode(), $shippingGroup, $deliveryTime, $price);
        }
    }

    /**
     * Returns the countries for a region
     *
     * @param $region
     * @return \Mageinn\Vendor\Model\ResourceModel\Region\Collection
     */
    public function getCountriesForRegion($region)
    {
        $regionCollection = $this->regionCollectionFactory->create();

        $regionCollection->addFieldToFilter('name', ['eq' => $region]);

        return $regionCollection;
    }

    /**
     * Create a regions array for reference
     */
    public function setRegionsArray()
    {
        if (empty($this->regionsArray)) {
            $regions = $this->regionCollectionFactory->create();

            $regions->addFieldToSelect('name')
                // Adding the ignore coding standards as the result is not a large one
                // @codingStandardsIgnoreStart
                ->distinct(true);
                // @codingStandardsIgnoreEnd

            foreach ($regions as $region) {
                $this->regionsArray[] = $region->getName();
            }
        }
    }

    /**
     * Creates a countries array for reference
     */
    public function setCountriesArray()
    {
        if (empty($this->countriesArray)) {
            $countries = $this->regionCollectionFactory->create();

            $countries->addFieldToSelect('country')
                ->addFieldToSelect('code')
                // Adding the ignore coding standards as the result is not a large one
                // @codingStandardsIgnoreStart
                ->distinct(true);
                // @codingStandardsIgnoreEnd

            foreach ($countries as $country) {
                $this->countriesArray[$country->getCountry()] = $country->getCode();
            }
        }
    }

    /**
     * Region check for values in CSV file
     *
     * @param $region
     * @param $row
     * @return bool
     */
    public function checkRegion($region, $row)
    {
        if (!in_array($region, $this->regionsArray)) {
            $this->addErrorMessage(__('Region %1 declared on row #%2 is undefined in the system', $region, $row));

            return false;
        }

        return true;
    }

    /**
     * Country check for values in CSV file
     *
     * @param $country
     * @param $row
     * @return bool
     */
    public function checkCountry($country, $row)
    {
        if (!in_array($country, array_keys($this->countriesArray))) {
            $this->addErrorMessage(__('Country %1 declared on row #%2 is undefined in the system', $country, $row));

            return false;
        }

        return true;
    }

    /**
     * Number check for time and rate values in CSV file
     *
     * @param $value
     * @param $row
     * @return bool
     */
    public function checkIsNumber($value, $row)
    {
        if (!is_numeric($value)) {
            $this->addErrorMessage(__('Value %1 declared on row #%2 is not a number', $value, $row));

            return false;
        }

        return true;
    }

    /**
     * Check is the file has the csv extension
     *
     * @param $file
     * @return bool
     */
    public function isCsv($file)
    {
        $fileDetails = $this->fileSystem->getPathInfo($file);

        if (!in_array($fileDetails['extension'], $this->allowedFileExtensions)) {
            return false;
        }

        return true;
    }
}
