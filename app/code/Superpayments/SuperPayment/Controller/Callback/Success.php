<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Controller\Callback;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;
use Superpayments\SuperPayment\Gateway\Config\Config;

class Success implements ActionInterface, HttpGetActionInterface
{
    /** @var Session $checkoutSession */
    private $checkoutSession;

    /** @var Order $order */
    private $order;

    /** @var OrderRepository $orderRepository */
    private $orderRepository;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var Config */
    private $config;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderRepository $orderRepository,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->messageManager = $context->getMessageManager();
        $this->request = $context->getRequest();
        $this->response = $context->getResponse();
        $this->redirect = $context->getRedirect();
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function execute(): ResponseInterface
    {
        try {
            $this->order = $this->checkoutSession->getLastRealOrder();

            if ($this->order->getState() == Order::STATE_PENDING_PAYMENT) {
                $this->order->setState(Order::STATE_PROCESSING);
                $this->order->setStatus(Order::STATE_PROCESSING);
            } elseif ($this->order->getStatus() == Order::STATE_CANCELED) {
                $this->order->addCommentToStatusHistory(
                    __('Order was cancelled by administrator during payment step by customer.'
                        . 'Payment is now complete.')
                );
            } else {
                $this->order->addCommentToStatusHistory(
                    'Customer has completed checkout. Payment successful. Redirecting to confirmation page.'
                );
            }
            $this->orderRepository->save($this->order);
            $this->checkoutSession->setLastQuoteId($this->order->getQuoteId());
            $this->checkoutSession->unsQuoteId();
        } catch (Exception $e) {
            $this->logger->critical('[SuperPayments] ' . $e->getMessage(), ['exception' => $e]);
        }

        return $this->redirect('checkout/onepage/success', ['_secure' => $this->config->isWebsiteSecure()]);
    }

    private function redirect(string $path, array $arguments = []): ResponseInterface
    {
        $this->redirect->redirect($this->response, $path, $arguments);
        return $this->response;
    }
}
