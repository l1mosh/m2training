<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Model\Product;

use Magento\Catalog\API\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use ML\DeveloperTest\Model\Product\CurrentCountry;

class NonSalableProduct
{
    protected const XML_SALE_RESTRICTION_ENABLED = 'ml_sales_restriction/general/enable';
    protected const XML_SALE_RESTRICTION_MESSAGE = 'ml_sales_restriction/general/message';

    private ScopeConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;
    private ProductRepositoryInterface $productRepository;
    private CurrentCountry $currentCountry;

    public function __construct(
        ScopeConfigInterface       $scopeConfig,
        StoreManagerInterface      $storeManager,
        ProductRepositoryInterface $productRepository,
        CurrentCountry             $currentCountry
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->currentCountry = $currentCountry;
    }

    public function isNonSalable($productId): bool
    {
        $product = $this->productRepository->getById($productId);
        $blockedCountries = $product->getBlockedCountries();
        if ($blockedCountries) {
            $currentcountryCode = $this->currentCountry->getCurrentCountryCode();
            //if current country are in the blocked countries.
            if (str_contains($blockedCountries, $currentcountryCode)) {
                return true;
            }
        }
        //if no blocked countries
        return false;
    }

    public function isEnabled(WebsiteInterface $website = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_SALE_RESTRICTION_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $website
        );
    }

    public function getMessage(WebsiteInterface $website = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_SALE_RESTRICTION_MESSAGE,
            ScopeInterface::SCOPE_WEBSITE,
            $website
        );
    }
}
