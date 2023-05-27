<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Gateway\Request;

class ExpireOfferDataBuilder extends AbstractDataBuilder
{
    protected function getUrl(array $buildSubject): string
    {
        return $this->config->getUrl() . sprintf(
            '%s/%s/expire',
            self::ENDPOINT_OFFERS,
            $buildSubject['cashback_offer_id']
        );
    }

    protected function getMethod(array $buildSubject): string
    {
        return self::HTTP_POST;
    }

    protected function getBody(array $buildSubject): ?array
    {
        return null;
    }
}
