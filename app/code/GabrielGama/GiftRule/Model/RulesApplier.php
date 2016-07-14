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

/**
 * Class RulesApplier
 * @package Magento\SalesRule\Model\Validator
 */
class RulesApplier
{

    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $validatorUtility;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var array
     */
    protected $appliedRuleIds;

    /**
     * @param \GabrielGama\GiftRule\Model\Utility $utility
     * @param \Magento\Catalog\Model\Product $product
     */
    public function __construct(
        \GabrielGama\GiftRule\Model\Utility $utility,
        \Magento\Catalog\Model\Product $product
    )
    {
        $this->product = $product;
        $this->validatorUtility = $utility;
        $this->appliedRuleIds = [];
    }

    /**
     * Apply rules to current order item
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \GabrielGama\GiftRule\Model\ResourceModel\Rule\Collection $rules
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function applyRules($item, $rules)
    {
        $address = $item->getAddress();
        /* @var $rule \GabrielGama\GiftRule\Model\Rule */
        foreach ($rules as $rule) {
            if (!$this->validatorUtility->canProcessRule($rule, $address)) {
                continue;
            }

            if (array_key_exists($rule->getId(), $this->appliedRuleIds)) {
                continue;
            }

            $this->applyRule($rule, $address);
            $this->appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

        }

        return $this;
    }


    /**
     * @param \GabrielGama\GiftRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return $this
     */
    protected function applyRule($rule, $address)
    {
        try {
            $productId = $this->product->getIdBySku($rule->getGiftSku());
            $this->product->getResource()->load($this->product, $productId);
            if ($this->product) {
                $this->product->addCustomOption('gabrielgama_gift', 1);
                $quote = $address->getQuote();
                $giftItem = $quote->addProduct($this->product, $rule->getGiftQty());
                $giftItem->setIsGift(1);
                $giftItem->setCustomPrice(0);
                $giftItem->setOriginalCustomPrice(0);
                $giftItem->getResource()->save($giftItem);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            //ToDo add exception treatment
        } catch (\Exception $e) {
            //ToDo add exception treatment
        }
        return $this;
    }


    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return $this
     */
    public function setAppliedGiftRuleIds(\Magento\Quote\Model\Quote\Item\AbstractItem $item)
    {
        $address = $item->getAddress();
        $quote = $item->getQuote();

        $item->setAppliedGiftRuleIds(join(',',  $this->appliedRuleIds));
        $address->setAppliedGiftRuleIds($this->validatorUtility->mergeIds($address->getAppliedGiftRuleIds(),  $this->appliedRuleIds));
        $quote->setAppliedGiftRuleIds($this->validatorUtility->mergeIds($quote->getAppliedGiftRuleIds(),  $this->appliedRuleIds));

        return $this;
    }

    /**
     * @return $this
     */
    public function resetAppliedRules()
    {
        $this->appliedRuleIds = [];

        return $this;
    }
}
