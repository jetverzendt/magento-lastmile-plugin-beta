<?php

class JetVerzendt_Shipping_Block_Adminhtml_Widget_Grid_Column_Renderer_Tracktrace extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {

        if (is_a($row, 'Mage_Sales_Model_Order_Shipment')) {
            $orderId = $row->getOrderId();
        } else {
            $orderId = $row->getEntityId();
        }

        $result   = '';
        $packages = Mage::getModel('jetverzendt_shipping/package')->loadByOrderId($orderId);


        if (isset($packages) && count($packages) > 0) {
            foreach ($packages as $package) {
                $result .= '<a target="_blank" href="' .  $this->escapeUrl($package->getTrackTraceUrl()) . '">' . $this->escapeHtml($package->getTrackTraceKey()) . '</a><br/>';
            }
        }

        return $result;

    }
}



