<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Gateway\Request;

use Magento\Sales\Api\Data\OrderInterface;
use Superpayments\SuperPayment\Model\Config\Source\Environment;

class CreatePaymentDataBuilder extends AbstractDataBuilder
{
    protected function getUrl(array $buildSubject): string
    {
        return $this->config->getUrl() . self::ENDPOINT_PAYMENTS;
    }

    protected function getMethod(array $buildSubject): string
    {
        return self::HTTP_POST;
    }

    protected function getBody(array $buildSubject): ?array
    {
        /** @var OrderInterface $order */
        $order = $buildSubject['order'];

        return [
            'cashbackOfferId' => $buildSubject['cashbackOfferId'],
            'successUrl' => $this->urlBuilder->getUrl(
                'superpayment/callback/success/ref/' . $order->getIncrementId() . '/',
                ['_secure' => $this->config->isWebsiteSecure()]
            ),
            'cancelUrl' => $this->urlBuilder->getUrl(
                'superpayment/callback/cancel/ref/' . $order->getIncrementId() . '/',
                ['_secure' => $this->config->isWebsiteSecure()]
            ),
            'failureUrl' => $this->urlBuilder->getUrl(
                'superpayment/callback/failure/ref/' . $order->getIncrementId() . '/',
                ['_secure' => $this->config->isWebsiteSecure()]
            ),
            'minorUnitAmount' => ($order->getGrandTotal() * 100),
            'currency' => $order->getOrderCurrency()->getCode(),
            'externalReference' => $order->getIncrementId(),
            'test' => ($this->config->getEnvironment() == Environment::SANDBOX),
        ];
    }
}
