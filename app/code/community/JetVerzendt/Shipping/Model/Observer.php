<?php

class JetVerzendt_Shipping_Model_Observer extends Mage_Core_Helper_Abstract
{

    public function salesQuoteCollectTotalsBefore(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote           = $observer->getQuote();
        $newHandlingFee  = Mage::getSingleton('core/session')->getLastmileShipmentFee();
        $newHandlingName = Mage::getSingleton('core/session')->getLastmileShipmentName();

        $store    = Mage::app()->getStore($quote->getStoreId());
        $carriers = Mage::getStoreConfig('carriers', $store);
        $currentShipmentMethod = $quote->getShippingAddress()->getShippingMethod();
        $jetShipmentMethod     = Mage::helper('jetverzendt_shipping')->getJetVerzendtShipmentMethod();
        foreach ($carriers as $carrierCode => $carrierConfig) {

            if ($carrierCode == $jetShipmentMethod && strpos($currentShipmentMethod, $carrierCode) === 0) {

                $carrierName = $store->getConfig("carriers/{$carrierCode}/name");
                if (!empty($newHandlingName)) {
                    $newHandlingName = $carrierName . ' (' . $newHandlingName . ')';
                } else {
                    $newHandlingName = $carrierName;
                }


                $store->setConfig("carriers/{$carrierCode}/handling_type", 'F'); #F - Fixed, P - Percentage
                $store->setConfig("carriers/{$carrierCode}/handling_fee", $newHandlingFee);
                $store->setConfig("carriers/{$carrierCode}/name", $newHandlingName);

            }

        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        //$quote->getShippingAddress()->collectShippingRates();
    }


    /**
     * Save last mile options into the current quote
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function checkoutSaveShippingMethod(Varien_Event_Observer $observer)
    {
        $lastmile     = array();
        $lastmileType = $observer->getEvent()->getRequest()->getParam('lastmile_type');
        $shipmentFee = 0;
        Mage::getSingleton('core/session')->setLastmileShipmentFee($shipmentFee); // reset fee

        if (isset($lastmileType) && $lastmileType == 'dhl_deliverdate') {
            $lastmile['type']                    = $lastmileType;
            $lastmile['lastmile_service']        = $observer->getEvent()->getRequest()->getParam('lastmile_service');
            $lastmile['lastmile_deliverdate']    = $observer->getEvent()->getRequest()->getParam('lastmile_deliverdate');
            $lastmile['lastmile_deliverperiod']  = $observer->getEvent()->getRequest()->getParam('lastmile_deliverperiod');
            $lastmile['lastmile_deliverevening'] = $observer->getEvent()->getRequest()->getParam('lastmile_deliverevening');

            if ($lastmile['lastmile_deliverevening']) {
                $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDhlEvening();
            }

            $lastmile['lastmile_fee'] = $shipmentFee;

        } else if (isset($lastmileType) && $lastmileType == 'dpd_saterday') {
            $lastmile['type']                 = $lastmileType;
            $lastmile['lastmile_service']     = $observer->getEvent()->getRequest()->getParam('lastmile_service');
            $lastmile['lastmile_deliverdate'] = $observer->getEvent()->getRequest()->getParam('lastmile_deliverdate');

            $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDpdSaterday();

            $lastmile['lastmile_fee'] = $shipmentFee;

        } else if (isset($lastmileType) && $lastmileType == 'fadello') {
            $lastmile['type'] = $lastmileType;

            $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceFadello();

            $lastmile['lastmile_fee'] = $shipmentFee;

        } else if (isset($lastmileType) && $lastmileType == 'nextdaypremium') {
            $lastmile['type'] = $lastmileType;

            $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceNextDayPremium();

            $lastmile['lastmile_fee'] = $shipmentFee;

        } else if (isset($lastmileType) && $lastmileType == 'parcelshop') {
            $lastmile['type']                            = $lastmileType;
            $lastmile['lastmile_service']                = $observer->getEvent()->getRequest()->getParam('lastmile_service');
            $lastmile['lastmile_parcelshop_id']          = $observer->getEvent()->getRequest()->getParam('lastmile_parcelshop_id');
            $lastmile['lastmile_parcelshop_description'] = $observer->getEvent()->getRequest()->getParam('lastmile_parcelshop_description');

            if ($lastmile['lastmile_service'] == 'DHL') {
                $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDhlParcelshop();
            } elseif ($lastmile['lastmile_service'] == 'DPD') {
                $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDpdParcelshop();
            }

            $lastmile['lastmile_fee'] = $shipmentFee;

        }

        if (!empty($lastmile)) {

            try {
                $observer->getEvent()->getQuote()
                    ->setJetLastMile(serialize($lastmile))
                    ->save();


                Mage::getSingleton('core/session')->setLastmileShipmentFee($shipmentFee);

            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        //Mage::log($lastmileType, null, 'jetverzendt.log');
        return $this;

    }

    /**
     * Save last mile from the quote to order
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function checkoutConvertQuoteToOrder(Varien_Event_Observer $observer)
    {
        if ($lastmile = $observer->getEvent()->getQuote()->getJetLastMile()) {
            try {
                $observer->getEvent()->getOrder()
                    ->setJetLastMile($lastmile);

            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }


}