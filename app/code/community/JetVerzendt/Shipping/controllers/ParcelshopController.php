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

            $street1 = $street[0];
            $street2 = $street[1];
            $zipcode = $shippingAddress->getPostcode();
            $city = $shippingAddress->getCity();

            if (!isset($zipcode) || empty($zipcode) || !isset($city) || empty($city) ) {
                $street1 = $this->getRequest()->getParam('street1');
                $street2 = $this->getRequest()->getParam('street2');
                $zipcode = $this->getRequest()->getParam('zipcode');
                $city = $this->getRequest()->getParam('city');
            }

            $address = $street1 . ' ' . $street2 . ', ' . $zipcode . ', ' . $city;


            if (isset($address)) {
                $block = $this->getLayout()->createBlock(
                    'Mage_Core_Block_Template',
                    'jet_verzendt_order_overview',
                    array('template' => 'jetverzendt/parcelshop/popup.phtml')
                )->setData('address', $address);
                $this->getResponse()->setBody($block->toHtml());
            }
            return '';
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

            //$zip_code = $this->getRequest()->getPost('zip_code');
            //$country  = $this->getRequest()->getPost('country');
            //$number   = $this->getRequest()->getPost('number');
            $latitude = $this->getRequest()->getPost('latitude');
            $longitude = $this->getRequest()->getPost('longitude');

            if (isset($latitude) && !empty($latitude) && isset($longitude) && !empty($longitude)) {
                $address['latitude'] = $latitude;
                $address['longitude'] = $longitude;

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

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    protected function _getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function saveSelectedOptionAction()
    {
        $lastmile = array();
        $lastmileType = $this->getRequest()->getParam('lastmile_type');

        $shipmentFee = 0;
        $shipmentName = '';
        Mage::getSingleton('core/session')->setLastmileShipmentFee($shipmentFee); // reset fee
        Mage::getSingleton('core/session')->setLastmileShipmentName(''); // reset fee name

        if (isset($lastmileType) && $lastmileType == 'dhl_deliverdate') {
            $lastmile['type'] = $lastmileType;
            $lastmile['lastmile_service'] = $this->getRequest()->getParam('lastmile_service');
            $lastmile['lastmile_deliverdate'] = $this->getRequest()->getParam('lastmile_deliverdate');
            $lastmile['lastmile_deliverperiod'] = $this->getRequest()->getParam('lastmile_deliverperiod');
            $lastmile['lastmile_deliverevening'] = $this->getRequest()->getParam('lastmile_deliverevening');

            if ($lastmile['lastmile_deliverevening']) {
                $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDhlEvening();
            }

            $lastmile['lastmile_fee'] = $shipmentFee;
            $shipmentName = 'Eigen bezorgmoment';

        } else if (isset($lastmileType) && $lastmileType == 'dpd_saterday') {
            $lastmile['type'] = $lastmileType;
            $lastmile['lastmile_service'] = $this->getRequest()->getParam('lastmile_service');
            $lastmile['lastmile_deliverdate'] = $this->getRequest()->getParam('lastmile_deliverdate');

            $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDpdSaterday();

            $lastmile['lastmile_fee'] = $shipmentFee;
            $shipmentName = 'Zaterdaglevering';

        } else if (isset($lastmileType) && $lastmileType == 'fadello') {
            $lastmile['type'] = $lastmileType;

            $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceFadello();

            $lastmile['lastmile_fee'] = $shipmentFee;
            $shipmentName = 'Same day delivery';

        } else if (isset($lastmileType) && $lastmileType == 'nextdaypremium') {
            $lastmile['type'] = $lastmileType;

            $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceNextDayPremium();

            $lastmile['lastmile_fee'] = $shipmentFee;
            $shipmentName = 'Next day premium';

        } else if (isset($lastmileType) && $lastmileType == 'parcelshop') {
            $lastmile['type'] = $lastmileType;
            $lastmile['lastmile_service'] = $this->getRequest()->getParam('lastmile_service');
            $lastmile['lastmile_parcelshop_id'] = $this->getRequest()->getParam('lastmile_parcelshop_id');
            $lastmile['lastmile_parcelshop_description'] = $this->getRequest()->getParam('lastmile_parcelshop_description');

            if ($lastmile['lastmile_service'] == 'DHL') {
                $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDhlParcelshop();
            } elseif ($lastmile['lastmile_service'] == 'DPD') {
                $shipmentFee = Mage::helper('jetverzendt_shipping')->getLastmilePriceDpdParcelshop();
            }

            $lastmile['lastmile_fee'] = $shipmentFee;
            $shipmentName = 'Bezorgen bij Parcelshop';

        }
        $result = array();
        if (!empty($lastmile)) {
            try {
                $this->_getOnepage()->getQuote()
                    ->setJetLastMile(serialize($lastmile))
                    ->save();
                Mage::getSingleton('core/session')->setLastmileShipmentFee($shipmentFee);
                Mage::getSingleton('core/session')->setLastmileShipmentName($shipmentName); // reset fee name
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
                Mage::getSingleton('core/session')->setLastmileShipmentName($shipmentName);
                $result['error'] = false;
            } catch (Exception $e) {
                Mage::logException($e);
                $result['error'] = true;
            }
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }


}