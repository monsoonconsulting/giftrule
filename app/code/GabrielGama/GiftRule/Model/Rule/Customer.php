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
namespace GabrielGama\GiftRule\Model\Rule;

class Customer extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('GabrielGama\GiftRule\Model\ResourceModel\Rule\Customer');
    }

    /**
     * Load by customer rule
     *
     * @param int $customerId
     * @param int $ruleId
     * @return $this
     */
    public function loadByCustomerRule($customerId, $ruleId)
    {
        $this->_getResource()->loadByCustomerRule($this, $customerId, $ruleId);
        return $this;
    }
}
