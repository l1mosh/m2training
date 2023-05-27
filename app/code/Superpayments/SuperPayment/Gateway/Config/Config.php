<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Payment\Gateway\Config\Config as PaymentsConfig;
use Superpayments\SuperPayment\Model\Config\Source\Environment;

class Config extends PaymentsConfig
{
    public const PAYMENT_CODE = 'super_payment_gateway';
    public const MODULE_CODE = 'Superpayments_SuperPayment';

    public const KEY_ACTIVE = 'active';
    public const KEY_ENVIRONMENT = 'environment';
    public const KEY_API_KEY = 'api_key';
    public const KEY_CONFIRMATION_KEY = 'confirmation_key';
    public const KEY_SANDBOX_API_KEY = 'sandbox_api_key';
    public const KEY_SANDBOX_CONFIRMATION_KEY = 'sandbox_confirmation_key';
    public const KEY_SORT_ORDER = 'sort_order';
    public const KEY_TITLE = 'title';
    public const KEY_ALLOW_SPECIFIC = 'allowspecific';
    public const KEY_SPECIFIC_COUNTRY = 'specificcountry';
    public const KEY_DEBUG = 'debug';
    public const KEY_USE_HTTPS = 'use_https';
    public const KEY_COLOR_SCHEME = 'color_scheme';
    public const KEY_GATEWAY_TIMEOUT = 'gateway_timeout';

    /** @var null|int $store */
    private $store;

    /** @var ModuleListInterface */
    private $moduleList;

    /** @var ProductMetadataInterface */
    private $productMetadata;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ModuleListInterface $moduleList,
        ProductMetadataInterface $productMetadata,
        string $methodCode = self::PAYMENT_CODE,
        string $pathPattern = PaymentsConfig::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);

        $this->moduleList = $moduleList;
        $this->productMetadata = $productMetadata;
        $this->store = null;
    }

    public function setStoreId(int $storeId = null): void
    {
        $this->store = $storeId;
    }

    public function getStoreId(): ?int
    {
        return $this->store;
    }

    public function isActive(): bool
    {
        return (bool) $this->getValue(self::KEY_ACTIVE, $this->getStoreId());
    }

    public function getModuleVersion(): ?string
    {
        $moduleInfo = $this->moduleList->getOne(self::MODULE_CODE);
        return $moduleInfo['setup_version'];
    }

    public function getMagentoVersion(): ?string
    {
        if ($value = $this->productMetadata->getVersion()) {
            return (string) $value;
        }
        return null;
    }

    public function getMagentoEdition(): ?string
    {
        if ($value = $this->productMetadata->getEdition()) {
            return (string) $value;
        }
        return null;
    }

    public function getEnvironment(): ?string
    {
        if ($value = $this->getValue(self::KEY_ENVIRONMENT, $this->getStoreId())) {
            return $value;
        }
        return null;
    }

    public function getApiKey(): ?string
    {
        if ($this->getEnvironment() == Environment::SANDBOX) {
            return ($this->getValue(self::KEY_SANDBOX_API_KEY, $this->getStoreId())) ?: null;
        } else {
            return ($this->getValue(self::KEY_API_KEY, $this->getStoreId())) ?: null;
        }
    }

    public function getConfirmationKey(): ?string
    {
        if ($this->getEnvironment() == Environment::SANDBOX) {
            return ($this->getValue(self::KEY_SANDBOX_CONFIRMATION_KEY, $this->getStoreId())) ?: null;
        } else {
            return ($this->getValue(self::KEY_CONFIRMATION_KEY, $this->getStoreId())) ?: null;
        }
    }

    public function getSortOrder(): int
    {
        return (int) $this->getValue(self::KEY_SORT_ORDER, $this->getStoreId());
    }

    public function getTitle(): ?string
    {
        if ($value = $this->getValue(self::KEY_TITLE, $this->getStoreId())) {
            return $value;
        }
        return null;
    }

    public function getAllowSpecific(): bool
    {
        return (bool) $this->getValue(self::KEY_ALLOW_SPECIFIC, $this->getStoreId());
    }

    public function getSpecificCountry(): ?array
    {
        if ($value = $this->getValue(self::KEY_SPECIFIC_COUNTRY, $this->getStoreId())) {
            return $value;
        }
        return null;
    }

    public function isDebugEnabled(): bool
    {
        return (bool) $this->getValue(self::KEY_DEBUG, $this->getStoreId());
    }

    public function isWebsiteSecure(): bool
    {
        return (bool) $this->getValue(self::KEY_USE_HTTPS, $this->getStoreId());
    }

    public function getUrl(): string
    {
        if ($this->getEnvironment() == 'production') {
            return 'https://api.superpayments.com/v2';
        }
        return 'https://api.test.superpayments.com/v2';
    }

    public function getColorScheme(): ?string
    {
        return $this->getValue(self::KEY_COLOR_SCHEME, $this->getStoreId());
    }

    public function getGatewayTimeout(): int
    {
        return (int) $this->getValue(self::KEY_GATEWAY_TIMEOUT, $this->getStoreId());
    }
}
