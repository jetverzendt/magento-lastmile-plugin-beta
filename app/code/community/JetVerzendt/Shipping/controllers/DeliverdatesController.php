<?php

/**
 * Class JetVerzendt_Shipping_DeliverdatesController
 */
class JetVerzendt_Shipping_DeliverdatesController extends Mage_Core_Controller_Front_Action
{

    /**
     *
     */
    public function indexAction()
    {

    }

    public function dpddatesAction()
    {
        // check if valid ajax request
        if ($this->getRequest()->isXmlHttpRequest()) {
            $address = Mage::getModel('checkout/session')->getQuote()->getShippingAddress();
            if (isset($address)) {
                $lastmilePriceDpdSaterday = Mage::helper('jetverzendt_shipping')->getLastmilePriceDpdSaterday();
                $lastmileSaterdayDpdTime = Mage::helper('jetverzendt_shipping')->getLastmileSaterdayDpdTime();
                $block                    = $this->getLayout()->createBlock(
                    'Mage_Core_Block_Template',
                    'jet_verzendt_order_overview',
                    array('template' => 'jetverzendt/deliverdates/dpd_dates.phtml')
                )->setData('lastmile_price_dpd_saterday', $lastmilePriceDpdSaterday)
                    ->setData('lastmile_saterday_dpd_time', $lastmileSaterdayDpdTime);
                echo $block->toHtml();
            }
            exit;
        }
    }


    /**
     * Find parcelshop controller
     */
    public function dhldatesAction()
    {

        // check if valid ajax request
        if ($this->getRequest()->isXmlHttpRequest()) {
            $address = Mage::getModel('checkout/session')->getQuote()->getShippingAddress();
            if (isset($address)) {
                $parcelshopAddress       = Mage::helper('jetverzendt_shipping')->getDeliverDates($address);
                $lastmilePriceDhlEvening = Mage::helper('jetverzendt_shipping')->getLastmilePriceDhlEvening();
                $block                   = $this->getLayout()->createBlock(
                    'Mage_Core_Block_Template',
                    'jet_verzendt_order_overview',
                    array('template' => 'jetverzendt/deliverdates/dhl_dates.phtml')
                )->setData('deliver_dates', $parcelshopAddress)
                    ->setData('lastmile_price_dhl_evening', $lastmilePriceDhlEvening);
                echo $block->toHtml();
            }
            exit;
        }
    }

}