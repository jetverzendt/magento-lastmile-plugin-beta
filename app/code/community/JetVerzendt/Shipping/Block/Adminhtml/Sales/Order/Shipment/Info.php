<?php

/**
 * Class JetVerzendt_Shipping_Shipment
 */
class JetVerzendt_Shipping_Block_Adminhtml_Sales_Order_Shipment_Info extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form {


    /**
     * @return mixed
     */
    public function getPackages() {
       return Mage::getModel('jetverzendt_shipping/package')->loadByOrderId($this->getShipment()->getOrderId());

    }


}