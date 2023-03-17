<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Model\Product;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Phrase;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class CurrentCountry
{
    protected const XML_GEOIP_API_LINK = 'ml_sales_restriction/general/api_link';
    protected const XML_GEOIP_API_KEY = 'ml_sales_restriction/general/access_key';

    private JsonFactory $jsonFactory;
    private File $file;
    private ScopeConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;

    public function __construct(
        JsonFactory           $jsonFactory,
        File                  $file,
        ScopeConfigInterface  $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->file = $file;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getCurrentCountryCode(): string
    {
        $response = json_decode($this->getApiContent(), true);
        return (isset($response['country_code'])) ? $response['country_code'] : 'no country';
    }

    public function getCurrentCountryName(): string
    {
        $response = json_decode($this->getApiContent(), true);
        return (isset($response['country_name'])) ? $response['country_name'] : 'no country name';
    }

    protected function getApiContent(): string
    {
        try {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            //$ipAddress = "104.93.28.0"; //dummy french ip
            $apiUrl = $this->getApiLink() . $ipAddress . '?access_key=' . $this->getApiKey();
            return $this->file->fileGetContents($apiUrl);
        } catch (FileSystemException $e) {
            throw new FileSystemException(__('Failed to read ' . $apiUrl));
        }
    }

    public function getApiLink(WebsiteInterface $website = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_GEOIP_API_LINK,
            ScopeInterface::SCOPE_WEBSITE,
            $website
        );
    }

    public function getApiKey(WebsiteInterface $website = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_GEOIP_API_KEY,
            ScopeInterface::SCOPE_WEBSITE,
            $website
        );
    }
}
