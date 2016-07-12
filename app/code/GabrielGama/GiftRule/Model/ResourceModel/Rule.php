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
namespace GabrielGama\GiftRule\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

class Rule extends \Magento\Rule\Model\ResourceModel\AbstractResource
{

    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'customer_group' => [
            'associations_table' => 'gabrielgama_giftrule_customer_group',
            'rule_id_field' => 'rule_id',
            'entity_id_field' => 'customer_group_id',
        ],
    ];

    /**
     * @var array
     */
    protected $customerGroupIds = [];
    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        $connectionName = null
    )
    {
        $this->string = $string;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('gabrielgama_giftrule', 'rule_id');
    }

    /**
     * Add customer group ids and website ids to rule data after load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadCustomerGroupIds($object);

        parent::_afterLoad($object);
        return $this;
    }


    /**
     * @param AbstractModel $object
     * @return void
     */
    public function loadCustomerGroupIds(AbstractModel $object)
    {
        if (!$this->customerGroupIds) {
            $this->customerGroupIds = (array)$this->getCustomerGroupIds($object->getId());
        }
        $object->setData('customer_group_ids', $this->customerGroupIds);
    }

    /**
     * Prepare sales rule's discount quantity
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(AbstractModel $object)
    {
        return $this;
    }

    /**
     * Bind sales rule to customer group(s) and website(s).
     * Save rule's associated store labels.
     * Save product attributes used in rule.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->bindRuleToEntity($object->getId(), $customerGroupIds, 'customer_group');
        }

        return parent::_afterSave($object);
    }


}
