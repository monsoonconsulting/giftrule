<?php
/**
 * GabrielGama <http://gabrielgama.com>
 *
 * DISCLAIMER
 *
 * Don't change this file if you will upgrade your module in the future.
 *
 * @category      GabrielGama
 * @package       GabrielGama_GiftRule
 *
 * @author        Gabriel da Gama <me@gabrielgama.com>
 */
namespace GabrielGama\GiftRule\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;


class Validator extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Rule source collection
     *
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    protected $_rules;

    /**
     * Defines if method \Magento\SalesRule\Model\Validator::reset() wasn't called
     * Used for clearing applied rule ids in Quote and in Address
     *
     * @var bool
     */
    protected $_isFirstTimeResetRun = true;

    /**
     * @var \GabrielGama\GiftRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $validatorUtility;

    /**
     * @var \Magento\SalesRule\Model\RulesApplier
     */
    protected $rulesApplier;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \GabrielGama\GiftRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param Utility $utility
     * @param RulesApplier $rulesApplier
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \GabrielGama\GiftRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \GabrielGama\GiftRule\Model\Utility $utility,
        \GabrielGama\GiftRule\Model\RulesApplier $rulesApplier,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->validatorUtility = $utility;
        $this->rulesApplier = $rulesApplier;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init validator
     * Init process load collection of rules for specific website,
     * customer group and coupon code
     *
     * @param $customerGroupId
     * @return $this
     */
    public function init($customerGroupId)
    {
        $this->setCustomerGroupId($customerGroupId);
        $key = $customerGroupId;

        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = $this->_collectionFactory->create()
                ->setValidationFilter(
                    $customerGroupId
                )
                ->addFieldToFilter('is_active', 1)
                ->load();
        }


        return $this;
    }

    /**
     * Get rules collection for current object state
     *
     * @return \GabrielGama\GiftRule\Model\ResourceModel\Rule\Collection
     */
    protected function _getRules()
    {
        $key = $this->getCustomerGroupId();
        return $this->_rules[$key];
    }

    /**
     * Can apply rules check
     *
     * @param AbstractItem $item
     * @return bool
     */
    public function canApplyRules(AbstractItem $item)
    {
        $address = $item->getAddress();
        foreach ($this->_getRules() as $rule) {
            if (!$this->validatorUtility->canProcessRule($rule, $address)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Reset quote and address applied rules
     *
     * @param Address $address
     * @return $this
     */
    public function reset(Address $address)
    {
        if ($this->_isFirstTimeResetRun) {
            $address->setAppliedGiftRuleIds('');
            $address->getQuote()->setAppliedGiftRuleIds('');
            $this->_isFirstTimeResetRun = false;
        }
        $this->rulesApplier->resetAppliedRules();
        $quote = $address->getQuote();
        $items = $quote->getItemsCollection(false);
        foreach ($items as $item) {
            if ($item->getIsGift()) {
                $item->delete();
            }
        }
        return $this;
    }

    /**
     * Quote item discount calculation process
     *
     * @param AbstractItem $item
     * @return $this
     */
    public function process(AbstractItem $item)
    {
        if ($item->getIsGift()) {
            return $this;
        }
        $this->rulesApplier->applyRules(
            $item,
            $this->_getRules()
        );
        $this->rulesApplier->setAppliedGiftRuleIds($item);

        return $this;
    }

}
