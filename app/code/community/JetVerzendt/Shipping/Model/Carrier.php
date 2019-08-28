<?php

class JetVerzendt_Shipping_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_code = 'foobar_customrate';
    protected $_isFixed = true;

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');

        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier('foobar_customrate');
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod('foobar_customrate');
        $method->setMethodTitle($this->getConfigData('name'));
        $method->setPrice(5);
        $method->setCost(2);

        $result->append($method);

        return $result;
    }

    public function getAllowedMethods()
    {
        return array('foobar_customrate' => $this->getConfigData('name'));
    }

}