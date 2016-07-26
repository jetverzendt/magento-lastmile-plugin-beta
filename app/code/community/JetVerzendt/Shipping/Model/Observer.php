<?php

class JetVerzendt_Shipping_Model_Observer extends Mage_Core_Helper_Abstract
{


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

        }else if (isset($lastmileType) && $lastmileType == 'fadello') {
            $lastmile['type']                 = $lastmileType;

            $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceFadello();

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

        if ( ! empty($lastmile)) {

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