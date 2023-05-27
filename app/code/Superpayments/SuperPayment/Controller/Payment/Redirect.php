<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Controller\Payment;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;
use Superpayments\SuperPayment\Gateway\Service\ApiServiceInterface;

class Redirect implements ActionInterface, HttpGetActionInterface
{
    /** @var Session $checkoutSession */
    private $checkoutSession;

    /** @var ApiServiceInterface $apiService */
    private $apiService;

    /** @var Order $order */
    private $order;

    /** @var OrderRepository $orderRepository */
    private $orderRepository;

    /** @var DataObjectFactory $dataObjectFactory */
    private $dataObjectFactory;

    /** @var CurrentCustomer $currentCustomer */
    private $currentCustomer;

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

    public function __construct(
        Context $context,
        ApiServiceInterface $apiService,
        Session $checkoutSession,
        OrderRepository $orderRepository,
        DataObjectFactory $dataObjectFactory,
        CurrentCustomer $currentCustomer,
        LoggerInterface $logger
    ) {
        $this->apiService = $apiService;
        $this->messageManager = $context->getMessageManager();
        $this->request = $context->getRequest();
        $this->response = $context->getResponse();
        $this->redirect = $context->getRedirect();
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->currentCustomer = $currentCustomer;
        $this->logger = $logger;
    }

    public function execute(): ResponseInterface
    {
        try {
            $this->order = $this->checkoutSession->getLastRealOrder();
            $this->order->setState(Order::STATE_PENDING_PAYMENT);
            $this->order->setStatus(Order::STATE_PENDING_PAYMENT);
            $this->order->setCanSendNewEmailFlag(false);
            $this->orderRepository->save($this->order);

            $data = [
                'order' => $this->order,
                'payment' => $this->order->getPayment(),
                'customer' => $this->currentCustomer,
                'cashbackOfferId' => $this->order->getPayment()->getAdditionalInformation('spCashbackOfferId'),
            ];

            $response = $this->apiService->execute($data);
            return $this->redirect($response->getData('redirectUrl'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->order->addCommentToStatusHistory(
                __('Error occurred during payment redirect step: ' . $e->getMessage()),
                Order::STATE_PENDING_PAYMENT,
                false
            );
            $this->orderRepository->save($this->order);
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
        }
        return $this->redirect('checkout/cart');
    }

    private function redirect(string $path, array $arguments = []): ResponseInterface
    {
        $this->redirect->redirect($this->response, $path, $arguments);
        return $this->response;
    }
}
