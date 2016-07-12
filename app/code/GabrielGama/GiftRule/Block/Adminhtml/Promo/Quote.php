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
namespace GabrielGama\GiftRule\Block\Adminhtml\Promo;

class Quote extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'promo_quote';
        $this->_headerText = __('Gift Cart Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}
