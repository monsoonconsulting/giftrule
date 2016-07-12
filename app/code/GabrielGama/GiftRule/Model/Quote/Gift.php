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
namespace GabrielGama\GiftRule\Model\Quote;

class Gift extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \GabrielGama\GiftRule\Model\Validator
     */
    protected $validator;


    /**
     * @param \GabrielGama\GiftRule\Model\Validator $validator
     */
    public function __construct(
        \GabrielGama\GiftRule\Model\Validator $validator
    ) {
        $this->setCode('gift');
        $this->validator = $validator;
    }

    /**
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();

        $this->validator->reset($address);

        $this->validator->init($quote->getCustomerGroupId());

        $items = $quote->getItemsCollection(false);
        if (!count($items)) {
            return $this;
        }

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {
            if ($item->getParentItem() || $item->getIsGift() || $item->isDeleted()) {
                continue;
            }
            $this->validator->process($item);
        }

        return $this;
    }

}
