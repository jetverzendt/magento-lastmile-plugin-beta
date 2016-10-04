<?php

/**
 * Class JetVerzendt_Shipping_Model_Resource_Package_Collection
 */
class JetVerzendt_Shipping_Model_Resource_Package_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('jetverzendt_shipping/package');
    }
}