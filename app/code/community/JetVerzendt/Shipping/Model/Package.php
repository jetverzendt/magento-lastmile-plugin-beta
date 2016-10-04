<?php

/**
 * Class JetVerzendt_Shipping_Model_Package
 */
class JetVerzendt_Shipping_Model_Package extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('jetverzendt_shipping/package');
    }


    public function loadByOrderId($orderId)
    {
        $collection = Mage::getModel('jetverzendt_shipping/package')->getCollection();
        $collection->addFieldToFilter('mage_order_id', $orderId);

        if ($collection->getSize() > 0 && $orderId > 0) {
            return $collection;
        }
        return false;
    }
}