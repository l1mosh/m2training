<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Plugin\Checkout\Cart;

use Magento\Checkout\Controller\Cart\Add as CheckoutAdd;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use ML\DeveloperTest\Model\Product\CurrentCountry;
use ML\DeveloperTest\Model\Product\NonSalableProduct;

class Add
{
    protected ManagerInterface $messageManager;
    protected RedirectFactory $resultRedirectFactory;
    private NonSalableProduct $nonSalableProduct;
    private CurrentCountry $currentCountry;

    public function __construct(
        Context           $context,
        NonSalableProduct $nonSalableProduct,
        CurrentCountry    $currentCountry
    ) {
        $this->messageManager = $context->getMessageManager();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->nonSalableProduct = $nonSalableProduct;
        $this->currentCountry = $currentCountry;
    }

    public function aroundExecute(CheckoutAdd $subject, callable $proceed)
    {
        if ($this->nonSalableProduct->isEnabled()) {
            $productId = $subject->getRequest()->getParam('product');
            if ($productId) {
                if ($this->nonSalableProduct->isNonSalable($productId)) {
                    $this->messageManager->addErrorMessage(
                        __($this->nonSalableProduct->getMessage() . ' '.
                            $this->currentCountry->getCurrentCountryName())
                    );
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
            }
        }
        // no changes in the add to cart.
        return $proceed();
    }
}
