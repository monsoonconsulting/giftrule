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
namespace GabrielGama\GiftRule\Model\ResourceModel\Rule;

class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('gabrielgama_giftrule_customer', 'rule_customer_id');
    }

    /**
     * Get rule usage record for a customer
     *
     * @param \Magento\SalesRule\Model\Rule\Customer $rule
     * @param int $customerId
     * @param int $ruleId
     * @return $this
     */
    public function loadByCustomerRule($rule, $customerId, $ruleId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable()
        )->where(
            'customer_id = :customer_id'
        )->where(
            'rule_id = :rule_id'
        );
        $data = $connection->fetchRow($select, [':rule_id' => $ruleId, ':customer_id' => $customerId]);
        if (false === $data) {
            // set empty data, as an existing rule object might be used
            $data = [];
        }
        $rule->setData($data);
        return $this;
    }
}
