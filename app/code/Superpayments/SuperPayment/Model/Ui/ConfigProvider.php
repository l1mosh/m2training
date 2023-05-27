<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Superpayments\SuperPayment\Gateway\Config\Config as SuperPaymentsConfig;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var SuperPaymentsConfig */
    private $config;

    /** @var UrlInterface */
    private $urlBuilder;

    /** @var RequestInterface */
    private $request;

    public function __construct(
        SuperPaymentsConfig $config,
        RequestInterface $request,
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    public function getConfig(): array
    {
        return [
            'payment' => [
                SuperPaymentsConfig::PAYMENT_CODE => [
                    'isActive' => $this->config->isActive(),
                    'title' => __($this->config->getTitle()),
                    'mode' => $this->config->getEnvironment(),
                    'debug' => $this->config->isDebugEnabled(),
                    'redirectUrl' => $this->urlBuilder->getUrl(
                        'superpayment/payment/redirect/',
                        ['_secure' => $this->request->isSecure()]
                    ),
                ],
            ],
        ];
    }
}
