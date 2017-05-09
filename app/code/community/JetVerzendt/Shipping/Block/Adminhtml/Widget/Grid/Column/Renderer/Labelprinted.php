<?php

class JetVerzendt_Shipping_Block_Adminhtml_Widget_Grid_Column_Renderer_Labelprinted extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if (is_a($row, 'Mage_Sales_Model_Order_Shipment')) {
            $orderId = $row->getOrderId();
        } else {
            $orderId = $row->getEntityId();
        }

        $shipment = Mage::getModel('jetverzendt_shipping/shipment')->load($orderId, 'mage_order_id');

        if ($shipment->getLabelPrinted() != '') {

            if ($shipment->getLabelPrinted() == 1) {
                return '<div class="yes">Ja</div>';
            } else {
                return '<div class="no">Nee</div>';
            }

        } else {
            return '';
        }


    }
}



