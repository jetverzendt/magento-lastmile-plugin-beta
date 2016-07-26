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

            if (isset($zip_code) && ! empty($zip_code) && isset($country) && ! empty($country) && isset($number) && ! empty($number)) {
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
                                $result[] = array('lat'    => $shop->latitude,
                                                  'lng'    => $shop->longitude,
                                                  'html'   => $block->toHtml(),
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


}