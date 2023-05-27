<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Controller\Update;

use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;
use Superpayments\SuperPayment\Gateway\Config\Config;

class Payment implements ActionInterface, HttpPostActionInterface, CsrfAwareActionInterface
{
    /** @var Order $order */
    private $order;

    /** @var OrderRepository $orderRepository */
    private $orderRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Http
     */
    private $response;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var Config */
    private $config;

    public function __construct(
        Context $context,
        OrderRepository $orderRepository,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->request = $context->getRequest();
        $this->response = $context->getResponse();
        $this->orderRepository = $orderRepository;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function execute(): ResponseInterface
    {
        try {
            //todo
            $this->response->setStatusCode(Http::STATUS_CODE_200)->setContent('OK');
        } catch (Exception $e) {
            $this->logger->critical('[SuperPayments] ' . $e->getMessage(), ['exception' => $e]);
        }

        return $this->response;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
