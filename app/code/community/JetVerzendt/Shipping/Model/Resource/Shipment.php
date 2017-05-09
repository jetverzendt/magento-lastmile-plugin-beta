<?php

/**
 * Class JetVerzendt_Shipping_Model_Resource_Shipment
 */
class JetVerzendt_Shipping_Model_Resource_Shipment extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('jetverzendt_shipping/shipment', 'id');

    }
}