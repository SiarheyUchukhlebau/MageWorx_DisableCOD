<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DisableCOD\Plugin;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Backend\Model\Auth\Session as BackendSession;
use Magento\OfflinePayments\Model\Cashondelivery;

class DisableCashOnDelivery
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @param CustomerSession $customerSession
     * @param BackendSession $backendSession
     */
    public function __construct(
        CustomerSession $customerSession,
        BackendSession $backendSession
    ) {
        $this->customerSession = $customerSession;
        $this->backendSession = $backendSession;
    }

    /**
     *
     * @param Cashondelivery $subject
     * @param                $result
     * @return bool
     */
    public function afterIsAvailable(Cashondelivery $subject, $result)
    {
        $condition = true; // Replace with your condition

        if ($condition) {
            return false;
        }

        return $result;
    }
}