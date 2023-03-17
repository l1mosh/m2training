<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use ML\DeveloperTest\Model\Product\Attribute\Source\Countries;

class CreateProductAttributeBlockedCountries implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $productEntityTypeId = Product::ENTITY;
        $eavSetup->addAttribute(
            $productEntityTypeId,
            'blocked_countries',
            [
                'group' => 'Product Details',
                'type' => 'varchar',
                'backend' => ArrayBackend::class,
                'frontend' => '',
                'label' => 'Blocked Countries',
                'input' => 'multiselect',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'used_in_product_listing' => true,
                'default' => '',
                'source' => Countries::class,
                'visible_on_front' => false,
                'apply_to' => '',
                'sort_order' => 10,
            ]
        );
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
