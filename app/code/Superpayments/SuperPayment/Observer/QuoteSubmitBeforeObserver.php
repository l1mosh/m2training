<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Superpayments\SuperPayment\Gateway\Service\ApiServiceInterface;

class QuoteSubmitBeforeObserver implements ObserverInterface
{
    /** @var ApiServiceInterface $apiService */
    private $apiService;

    public function __construct(ApiServiceInterface $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var OrderInterface $order */
        $order = $event->getOrder();
        /** @var CartInterface $quote */
        $quote = $event->getQuote();

        $data = [
            'quote' => $quote,
            'page' => 'Checkout',
            'output' => 'calculation',
            'response' => [],
        ];

        $response = $this->apiService->execute($data);
        $order->getPayment()->setAdditionalInformation(
            'spCashbackOfferId',
            $response->getData('cashbackOfferId')
        );
        $order->addCommentToStatusHistory('Cashback Offer Id: ' . $response->getData('cashbackOfferId'));

        return $this;
    }
}
