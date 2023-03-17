<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Model\Product\Attribute\Source;

use Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;

class Countries extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    private CountryCollection $countryCollection;

    public function __construct(
        CountryCollection $countryCollection
    ) {
        $this->countryCollection = $countryCollection;
    }

    public function getAllOptions()
    {
        if (null === $this->_options) {
            $this->_options = $this->countryCollection->loadData()->setForegroundCountries(
                ''
            )->toOptionArray(
                false
            );
        }
        return $this->_options;
    }

    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
