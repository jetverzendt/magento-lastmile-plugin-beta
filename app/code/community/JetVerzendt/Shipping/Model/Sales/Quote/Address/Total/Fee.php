<?php

class JetVerzendt_Shipping_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_code = 'fee';

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        
        parent::collect($address);

        $this->_setAmount(0);
        $this->_setBaseAmount(0);

        $items = $this->_getAddressItems($address);
        if ( ! count($items)) {
            return $this; //this makes only address type shipping to come through
        }

        $shipmentFee = Mage::getSingleton('core/session')->getLastmileShipmentFee();
        $fee         = (isset($shipmentFee) && $shipmentFee > 0) ? (float)$shipmentFee : 0;
        $quote       = $address->getQuote();


        $address->setFeeAmount($fee);
        $address->setBaseFeeAmount($fee);

        $quote->setFeeAmount($fee);

        $address->setGrandTotal($address->getGrandTotal() + $address->getFeeAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseFeeAmount());

    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $shipmentFee = $address->getFeeAmount();
        if ($shipmentFee > 0) {
            $address->addTotal(
                array(
                    'code'  => $this->getCode(),
                    'title' => 'Meerkosten Lastmile',
                    'value' => $shipmentFee
                )
            );

            return $this;
        }

    }
}