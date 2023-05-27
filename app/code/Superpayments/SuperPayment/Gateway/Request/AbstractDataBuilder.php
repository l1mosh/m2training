<?php

namespace Superpayments\SuperPayment\Gateway\Request;

use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;
use Superpayments\SuperPayment\Gateway\Config\Config;

abstract class AbstractDataBuilder implements BuilderInterface
{
    public const ENDPOINT_PAYMENTS = '/payments';
    public const ENDPOINT_OFFERS = '/offers';
    public const ENDPOINT_REFUNDS = '/refunds';
    public const HTTP_POST = 'POST';
    public const HTTP_GET = 'GET';

    /** @var Config $config */
    protected $config;

    /** @var Logger */
    protected $logger;

    /** @var UrlInterface */
    protected $urlBuilder;

    public function __construct(
        Config $config,
        UrlInterface $urlBuilder,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
    }

    abstract protected function getUrl(array $buildSubject): string;

    abstract protected function getMethod(array $buildSubject): string;

    /** @return array|string */
    abstract protected function getBody(array $buildSubject): ?array;

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        if (isset($buildSubject['store'])) {
            $this->config->setStoreId((int) $buildSubject['store']->getId());
        }

        return [
            'url' => $this->getUrl($buildSubject),
            'method' => $this->getMethod($buildSubject),
            'headers' => $this->getHeaders($buildSubject),
            'body' => $this->getBody($buildSubject),
        ];
    }

    protected function getHeaders(array $buildSubject): array
    {
        return [
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'version' => $this->config->getModuleVersion(),
            'magento-version' => $this->config->getMagentoVersion(),
            'magento-edition' => $this->config->getMagentoEdition(),
            'checkout-api-key' => $this->config->getApiKey(),
            'referer' => $buildSubject['store']->getBaseUrl(),
        ];
    }
}
