<?php

class JetVerzendt_Shipping_Block_Lastmile extends Mage_Core_Block_Template
{

    public function _toHtml()
    {
        if (Mage::helper('jetverzendt_shipping')->isLastmileEnabled()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}