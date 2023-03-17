<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\PageBuilder\Plugin;

use Hyva\Theme\Service\CurrentTheme;
use Magento\CatalogWidget\Block\Product\ProductsList;

class OverridePageBuilderTemplates
{
    const GRID_WIDGET_TEMPLATE = 'Magento_CatalogWidget::product/widget/content/grid.phtml';
    const GRID_WIDGET_OVERRIDE = 'Hyva_PageBuilder::catalog/product/widget/content/grid.phtml';
    const CAROUSEL_WIDGET_TEMPLATE = 'Magento_PageBuilder::catalog/product/widget/content/carousel.phtml';
    const CAROUSEL_WIDGET_OVERRIDE = 'Hyva_PageBuilder::catalog/product/widget/content/carousel.phtml';

    /**
     * @var CurrentTheme
     */
    protected $theme;

    public function __construct(CurrentTheme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Templates are overridden via this plugin as Page Builder declares them within JavaScript files
     * See: vendor/magento/module-page-builder/view/adminhtml/web/js/content-type/products/mass-converter
     *
     * @param ProductsList $subject
     * @param string|null $result
     *
     * @return string|null
     */
    public function afterGetTemplate(ProductsList $subject, ?string $result): ?string
    {
        if ($this->theme->isHyva()) {
            if (strpos($result, self::GRID_WIDGET_TEMPLATE) !== false) {
                return self::GRID_WIDGET_OVERRIDE;
            }

            if (strpos($result, self::CAROUSEL_WIDGET_TEMPLATE) !== false) {
                return self::CAROUSEL_WIDGET_OVERRIDE;
            }
        }
        return $result;
    }
}
