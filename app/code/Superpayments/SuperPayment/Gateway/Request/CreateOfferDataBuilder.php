<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Gateway\Request;

use Exception;
use Magento\Quote\Api\Data\CartInterface;
use Superpayments\SuperPayment\Model\Config\Source\Environment;

class CreateOfferDataBuilder extends AbstractDataBuilder
{
    protected function getUrl(array $buildSubject): string
    {
        return $this->config->getUrl() . self::ENDPOINT_OFFERS;
    }

    protected function getMethod(array $buildSubject): string
    {
        return self::HTTP_POST;
    }

    protected function getBody(array $buildSubject): ?array
    {
        /** @var CartInterface $quote */
        $quote = $buildSubject['quote'];

        return [
            'minorUnitAmount' => ((int) ($quote->getGrandTotal() * 100)) ?: (0.01 * 100),
            'page' => $buildSubject['page'] ?: 'Checkout',
            'output' => $buildSubject['output'] ?: 'both',
            'scheme' => $this->config->getColorScheme(),
            'test' => ($this->config->getEnvironment() == Environment::SANDBOX),
            'cart' => [
                'id' => $quote->getId() ?: 'unknown-' . time(),
                'items' => $this->getItems($quote),
            ],
        ];
    }

    protected function getItems(CartInterface $quote): array
    {
        $items = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            try {
                $item = [
                    'name' => $item->getName(),
                    'url' => $item->getProduct()->getUrlModel()->getUrl($item->getProduct()),
                    'quantity' => (int) $item->getQty(),
                    'minorUnitAmount' => (int) ($item->getPrice() * 100),
                ];
                $items[] = $item;
            } catch (Exception $e) {
                $this->logger->error('[SuperPayment] ' . $e->getMessage(), ['exception' => $e]);
            }
        }

        if (empty($items)) {
            $items[] = [
                'name' => 'empty',
                'url' => 'http://empty.com/',
                'quantity' => (int) 1,
                'minorUnitAmount' => (int) (0.01 * 100),
            ];
        }

        return $items;
    }
}
