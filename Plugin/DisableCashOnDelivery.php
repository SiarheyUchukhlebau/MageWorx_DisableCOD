<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DisableCOD\Plugin;

use Magento\Framework\App\State;
use Magento\Checkout\Model\Session as CheckoutFrontendSession;
use Magento\Backend\Model\Session\Quote as CheckoutBackendSession;

class DisableCashOnDelivery
{
    /**
     * @var CheckoutFrontendSession|CheckoutBackendSession
     */
    protected $session;

    /**
     * @param CheckoutFrontendSession $checkoutFrontendSession
     * @param CheckoutBackendSession  $checkoutBackendSession
     * @param State                   $state
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        CheckoutFrontendSession $checkoutFrontendSession,
        CheckoutBackendSession $checkoutBackendSession,
        State $state
    ) {
        /**
         * Using the area code we detect which session object should we use later to obtain actual quote object.
         * 
         * Possible sessions:
         * \Magento\Checkout\Model\Session for the frontend
         * \Magento\Backend\Model\Session\Quote for the backend (admin creates new order)
         */
        if ($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $this->session = $checkoutBackendSession;
        } else {
            $this->session = $checkoutFrontendSession;
        }
    }

    /**
     * @param \Magento\OfflinePayments\Model\Cashondelivery $subject
     * @param callable                                      $proceed
     * @param \Magento\Quote\Api\Data\CartInterface|null    $quote
     * @return bool
     */
    public function aroundIsAvailable(
        \Magento\OfflinePayments\Model\Cashondelivery $subject,
        callable $proceed,
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        /**
         * Before doing our advanced validation we call main method
         */
        $result = $proceed($quote);

        /**
         * The quote can be null on the frontend, in this case we get it from corresponding session
         * @see \Magento\OfflinePayments\Model\InstructionsConfigProvider::getConfig()
         */
        if ($quote === null) {
            $quote = $this->session->getQuote();
        }

        /**
         * Disable Cash on Delivery for the addresses without post code.
         * Change this condition to desired one.
         */
        if (!$quote->getShippingAddress()->getPostcode()) {
            return false;
        }

        return $result;
    }
}