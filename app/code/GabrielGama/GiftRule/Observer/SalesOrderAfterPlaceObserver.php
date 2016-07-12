<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace GabrielGama\GiftRule\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderAfterPlaceObserver implements ObserverInterface
{
    /**
     * @var \GabrielGama\GiftRule\Model\RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var \GabrielGama\GiftRule\Model\Rule\CustomerFactory
     */
    protected $_ruleCustomerFactory;

    /**
     * @param \GabrielGama\GiftRule\Model\RuleFactory $ruleFactory
     * @param \GabrielGama\GiftRule\Model\Rule\CustomerFactory $ruleCustomerFactory
     */
    public function __construct(
        \GabrielGama\GiftRule\Model\RuleFactory $ruleFactory,
        \GabrielGama\GiftRule\Model\Rule\CustomerFactory $ruleCustomerFactory
    ) {
        $this->_ruleFactory = $ruleFactory;
        $this->_ruleCustomerFactory = $ruleCustomerFactory;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order) {
            return $this;
        }

        // lookup rule ids
        $ruleIds = explode(',', $order->getAppliedGiftRuleIds());
        $ruleIds = array_unique($ruleIds);

        $ruleCustomer = null;
        $customerId = $order->getCustomerId();

        // use each rule (and apply to customer, if applicable)
        foreach ($ruleIds as $ruleId) {
            if (!$ruleId) {
                continue;
            }
            /** @var \Magento\SalesRule\Model\Rule $rule */
            $rule = $this->_ruleFactory->create();
            $rule->load($ruleId);
            if ($rule->getId()) {
                $rule->setTimesUsed($rule->getTimesUsed() + 1);
                $rule->save();

                if ($customerId) {
                    /** @var \Magento\SalesRule\Model\Rule\Customer $ruleCustomer */
                    $ruleCustomer = $this->_ruleCustomerFactory->create();
                    $ruleCustomer->loadByCustomerRule($customerId, $ruleId);

                    if ($ruleCustomer->getId()) {
                        $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed() + 1);
                    } else {
                        $ruleCustomer->setCustomerId($customerId)->setRuleId($ruleId)->setTimesUsed(1);
                    }
                    $ruleCustomer->save();
                }
            }
        }
        return $this;
    }
}
