<?php

class JetVerzendt_Shipping_ParcelshopController extends Mage_Core_Controller_Front_Action
{

    /**
     *
     */
    public function indexAction()
    {

    }

    /**
     * Find parcelshop maps
     */
    public function popupAction()
    {
        // check if valid ajax request
        if ($this->getRequest()->isXmlHttpRequest()) {

            $shippingAddress = Mage::getModel('checkout/session')->getQuote()->getShippingAddress();

            $street = Mage::helper('jetverzendt_shipping')->splitStreet($shippingAddress->getData('street'));

            $address = $street[0] . ' ' . $street[1] . ', ' . $shippingAddress->getPostcode() . ', ' . $shippingAddress->getCity();


            if (isset($address)) {
                $block = $this->getLayout()->createBlock(
                    'Mage_Core_Block_Template',
                    'jet_verzendt_order_overview',
                    array('template' => 'jetverzendt/parcelshop/popup.phtml')
                )->setData('address', $address);
                echo $block->toHtml();
            }

            exit;
        }
    }

    /**
     * Get parcelshop locations
     */
    public function dataAction()
    {

        // check if valid ajax request
        if ($this->getRequest()->isXmlHttpRequest()) {

            $result = array();

            $zip_code = $this->getRequest()->getPost('zip_code');
            $country  = $this->getRequest()->getPost('country');
            $number   = $this->getRequest()->getPost('number');

            if (isset($zip_code) && !empty($zip_code) && isset($country) && !empty($country) && isset($number) && !empty($number)) {
                $address['zip_code'] = $zip_code;
                $address['country']  = $country;
                $address['number']   = $number;

                $parcelshops = Mage::helper('jetverzendt_shipping')->getParcelShops($address);

                if (isset($parcelshops->parcel_shops) && is_object($parcelshops->parcel_shops)) {
                    foreach ($parcelshops->parcel_shops as $shipper => $val) {
                        if (is_array($val)) {
                            foreach ($val as $shop) {


                                // Get info window content
                                $block = $this->getLayout()->createBlock(
                                    'Mage_Core_Block_Template',
                                    'jet_verzendt_order_overview',
                                    array('template' => 'jetverzendt/parcelshop/parcelshop_infowindow.phtml')
                                )->setData('shop', $shop)->setData('shipper', $shipper);

                                // Get marker
                                if (isset($shipper) && $shipper == 'DHL') {
                                    $marker = Mage::getDesign()->getSkinUrl('images/jetverzendt/marker_dhl.png', array('_secure' => true));
                                } else {
                                    $marker = Mage::getDesign()->getSkinUrl('images/jetverzendt/marker_dpd.png', array('_secure' => true));

                                }
                                $result[] = array('lat' => $shop->latitude,
                                                  'lng' => $shop->longitude,
                                                  'html' => $block->toHtml(),
                                                  'marker' => $marker);
                            }
                        }
                    }
                }
            }

            echo json_encode($result);
            exit;
        }
    }

    protected function _getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function saveSelectedOptionAction()
    {
        $lastmile     = array();
        $lastmileType = $this->getRequest()->getParam('lastmile_type');

        $shipmentFee = 0;
        Mage::getSingleton('core/session')->setLastmileShipmentFee($shipmentFee); // reset fee

        if (isset($lastmileType) && $lastmileType == 'dhl_deliverdate') {
            $lastmile['type']                    = $lastmileType;
            $lastmile['lastmile_service']        = $this->getRequest()->getParam('lastmile_service');
            $lastmile['lastmile_deliverdate']    = $this->getRequest()->getParam('lastmile_deliverdate');
            $lastmile['lastmile_deliverperiod']  = $this->getRequest()->getParam('lastmile_deliverperiod');
            $lastmile['lastmile_deliverevening'] = $this->getRequest()->getParam('lastmile_deliverevening');

            if ($lastmile['lastmile_deliverevening']) {
                $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDhlEvening();
            }

            $lastmile['lastmile_fee'] = $shipmentFee;

        } else if (isset($lastmileType) && $lastmileType == 'dpd_saterday') {
            $lastmile['type']                 = $lastmileType;
            $lastmile['lastmile_service']     = $this->getRequest()->getParam('lastmile_service');
            $lastmile['lastmile_deliverdate'] = $this->getRequest()->getParam('lastmile_deliverdate');

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
            $lastmile['lastmile_service']                = $this->getRequest()->getParam('lastmile_service');
            $lastmile['lastmile_parcelshop_id']          = $this->getRequest()->getParam('lastmile_parcelshop_id');
            $lastmile['lastmile_parcelshop_description'] = $this->getRequest()->getParam('lastmile_parcelshop_description');

            if ($lastmile['lastmile_service'] == 'DHL') {
                $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDhlParcelshop();
            } elseif ($lastmile['lastmile_service'] == 'DPD') {
                $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDpdParcelshop();
            }

            $lastmile['lastmile_fee'] = $shipmentFee;

        }
        $result = array();
        if (!empty($lastmile)) {
            try {
                $this->_getOnepage()->getQuote()
                    ->setJetLastMile(serialize($lastmile))
                    ->save();
                Mage::getSingleton('core/session')->setLastmileShipmentFee($shipmentFee);
                $result['error'] = false;
            } catch (Exception $e) {
                Mage::logException($e);
                $result['error'] = true;
            }
        } else {
            try {
                $this->_getOnepage()->getQuote()
                    ->setJetLastMile('')
                    ->save();
                Mage::getSingleton('core/session')->setLastmileShipmentFee(0);
                $result['error'] = false;
            } catch (Exception $e) {
                Mage::logException($e);
                $result['error'] = true;
            }
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }


}