<?php

/**
 * Class JetVerzendt_Shipping_Model_Resource_Package
 */
class JetVerzendt_Shipping_Model_Resource_Package extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('jetverzendt_shipping/package', 'package_id');
    }
}