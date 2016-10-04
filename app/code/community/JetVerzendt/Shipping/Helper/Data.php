<?php

/**
 * Class JetVerzendt_Shipping_Helper_Data
 */
class JetVerzendt_Shipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    const STORE_CONFIG_PATH_TOKEN = 'jetverzendt/jetverzendt_authorisation/jetverzendt_token';
    const STORE_CONFIG_PATH_LIVE_STATUS = 'jetverzendt/jetverzendt_authorisation/jetverzendt_live_status';

    const STORE_CONFIG_PATH_SHIPPING_SHIPPERS = 'jetverzendt/jetverzendt_carriers/jetverzendt_shipping_shippers';
    const STORE_CONFIG_PATH_SHIPPING_METHODS = 'jetverzendt/jetverzendt_carriers/jetverzendt_shipping_methods';

    const STORE_CONFIG_PATH_PRINTER_METHODS = 'jetverzendt/jetverzendt_printer/jetverzendt_printer_methods';
    const STORE_CONFIG_PATH_PRINTER_LABEL = 'jetverzendt/jetverzendt_printer/jetverzendt_printer_label';

    const STORE_CONFIG_PATH_AUTO_TRACK_TRACE = 'jetverzendt/jetverzendt_auto_track_trace/jetverzendt_track_trace';

    // Last mile: yes or no
    const STORE_CONFIG_PATH_LASTMILE_ENABLE = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_enable';
    const STORE_CONFIG_PATH_LASTMILE_STATUS_COMPLETE = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_status_complete';

    // DHL Last Mile Delivery
    const STORE_CONFIG_PATH_LASTMILE_DELIVERY_DHL_ENABLE = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_delivery_dhl_enable';
    const STORE_CONFIG_PATH_LASTMILE_DELIVERY_DHL_TIME = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_delivery_dhl_time';
    const STORE_CONFIG_PATH_LASTMILE_PRICE_DHL_EVENING = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_price_dhl_evening';

    // DPD Saterday Delivery
    const STORE_CONFIG_PATH_LASTMILE_SATERDAY_DPD_ENABLE = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_saterday_dpd_enable';
    const STORE_CONFIG_PATH_LASTMILE_SATERDAY_DPD_TIME = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_saterday_dpd_time';
    const STORE_CONFIG_PATH_LASTMILE_SATERDAY_DPD_PRICE = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_saterday_dpd_price';

    // Fadello
    const STORE_CONFIG_PATH_LASTMILE_FADELLO_ENABLE = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_fadello_enable';
    const STORE_CONFIG_PATH_LASTMILE_FADELLO_TIME = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_fadello_time';
    const STORE_CONFIG_PATH_LASTMILE_FADELLO_PRICE = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_fadello_price';

    // DHL Parcelshop
    const STORE_CONFIG_PATH_LASTMILE_DHL_PARCELSHOP_ENABLE = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_dhl_parcelshop_enable';
    const STORE_CONFIG_PATH_LASTMILE_PRICE_DHL_PARCELSHOP = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_price_dhl_parcelshop';

    // DPD Parcelshop
    const STORE_CONFIG_PATH_LASTMILE_DPD_PARCELSHOP_ENABLE = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_dpd_parcelshop_enable';
    const STORE_CONFIG_PATH_LASTMILE_PRICE_DPD_PARCELSHOP = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_price_dpd_parcelshop';


    const STORE_CONFIG_PATH_LASTMILE_GOOGLE_MAPS_KEY = 'jetverzendt/jetverzendt_lastmile/jetverzendt_lastmile_google_maps_key';


    /**
     *
     * Check if an order in the Jet Verzendt config setting default is assigned to Jet Verzendt
     *
     * @param $orderId
     *
     * @return bool
     */
    public function isDefaultJetVerzendtOrder($orderId)
    {
        $value           = false;
        $shippingMethods = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_SHIPPING_METHODS, Mage::app()->getStore()
        );
        $order           = Mage::getModel('sales/order')->load($orderId);

        if ($shippingMethods && $order) {
            $shippingMethods = explode(',', $shippingMethods);
            foreach ($shippingMethods as $method) {
                if (strpos($order->getShippingMethod(), $method . '_') === 0) {
                    return true;
                }
            }
        }

        return false;

    }


    /**
     * Get the default Jet Verzendt shipment method (as example: returns: "flatrate")
     *
     * @return mixed
     */
    public function getJetVerzendtShipmentMethod()
    {
        return Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_SHIPPING_METHODS, Mage::app()->getStore()
        );
    }

    /**
     *
     * Check if an order is an order assigned to Jet
     *
     * @param $orderId
     *
     * @return bool
     */
    public function isJetVerzendtOrder($orderId)
    {

        $collection = Mage::getModel('jetverzendt_shipping/shipment')
            ->getCollection();
        $collection->addFieldToFilter('mage_order_id', $orderId);

        if ($collection->getSize() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Check if lastmile is enabled
     *
     * @return mixed
     */
    public function isLastmileEnabled()
    {
        return Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_ENABLE, Mage::app()->getStore()
        );
    }

    /**
     * Check if lastmile is enabled
     *
     * @return mixed
     */
    public function isLastMileStatusComplete()
    {
        return Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_STATUS_COMPLETE, Mage::app()->getStore()
        );
    }


    /**
     * @return mixed
     */
    public function isLastmileDeliveryDhlEnable()
    {
        return Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_DELIVERY_DHL_ENABLE,
            Mage::app()->getStore()
        );
    }


    /**
     * @return mixed
     */
    public function isLastmileSaterdayDpdEnable()
    {
        return Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_SATERDAY_DPD_ENABLE,
            Mage::app()->getStore()
        );
    }


    /**
     * @return mixed
     */
    public function isLastmileDpdParcelshopEnable()
    {
        return Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_DPD_PARCELSHOP_ENABLE,
            Mage::app()->getStore()
        );
    }

    /**
     * @return mixed
     */
    public function isLastmileDhlParcelshopEnable()
    {
        return Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_DHL_PARCELSHOP_ENABLE,
            Mage::app()->getStore()
        );
    }

    /**
     * @return mixed
     */
    public function isLastmileFadelloEnable()
    {
        return Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_FADELLO_ENABLE,
            Mage::app()->getStore()
        );
    }


    /**
     * Check with a date if the DHL deliver box can be shown
     *
     * @return mixed
     */
    public function showDhlDeliverPeriod($input)
    {
        // check deliver day
        $deliverDay = Mage::getModel('core/date')->date(
            'Ymd', strtotime($input)
        );

        $dailyDeliveryTime = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_DELIVERY_DHL_TIME,
            Mage::app()->getStore()
        );
        $dailyDeliveryTime = (isset($dailyDeliveryTime)
            && !empty($dailyDeliveryTime)) ? str_replace(
            ',', '', $dailyDeliveryTime
        ) : '000000';

        // get next deliver day
        switch (Mage::getModel('core/date')->date('w')) {
            case 6: // saterday
                $nextDeliverDay = Mage::getModel('core/date')->date(
                    'Ymd', strtotime('+3 days')
                );
                break;
            case 0: // sunday
                $nextDeliverDay = Mage::getModel('core/date')->date(
                    'Ymd', strtotime('+2 days')
                );
                break;
            default: // other days
                $nextDeliverDay = Mage::getModel('core/date')->date(
                    'Ymd', strtotime('+1 day')
                );
                break;
        }

        if ($deliverDay > $nextDeliverDay) {
            return true;
        } else if ($deliverDay == $nextDeliverDay) {
            if (Mage::getModel('core/date')->date('His') < $dailyDeliveryTime) {
                return true;
            }
        }

        return false;

    }


    /**
     * @return mixed|string
     */
    public function getLastmileSaterdayDpdTime()
    {
        $deliveryTime = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_SATERDAY_DPD_TIME,
            Mage::app()->getStore()
        );
        $deliveryTime = (isset($deliveryTime) && !empty($deliveryTime))
            ? str_replace(',', '', $deliveryTime) : '000000';

        return $deliveryTime;
    }

    /**
     * @return mixed|string
     */
    public function getLastmileFadelloTime()
    {
        $deliveryTime = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_FADELLO_TIME,
            Mage::app()->getStore()
        );
        $deliveryTime = (isset($deliveryTime) && !empty($deliveryTime))
            ? str_replace(',', '', $deliveryTime) : '000000';

        return $deliveryTime;
    }


    /**
     * @param $orderId
     *
     * @return mixed
     */
    public function getPackages($orderId)
    {
        return Mage::getModel('jetverzendt_shipping/package')->loadByOrderId(
            $orderId
        );

    }


    /**
     * Get Jet Verzendt Shipment Shippers
     *
     * @return array
     */
    public function getShipmentShippers()
    {
        $shipmentShippers = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_SHIPPING_SHIPPERS, Mage::app()->getStore()
        );

        return explode(',', $shipmentShippers);
    }


    /**
     * Try to split 1 street to 2 streets
     *
     * @param $street
     *
     * @return array
     */
    public function splitStreet($street)
    {
        $street = explode(
            "\n", $street
        );
        $street = array_map('trim', $street);
        if (empty($street[1])) { // if no/empty street 1, try to split street 0 field.
            $streetArr = explode(' ', $street[0]);
            if (count($streetArr) >= 2) {
                $street[1] = end($streetArr);
                $street[0] = implode(' ', array_slice($streetArr, 0, -1));
            } else {
                $street[1] = isset($street[1]) ? $street[1] : '';
                $street[0] = isset($street[0]) ? $street[0] : '';
            }
        }

        return $street;
    }


    /**
     * Send an order to the Jet Verzendt server
     *
     * @param $order
     * @param $postData
     *
     * @return mixed
     */
    public function sendOrdersToJetVerzendt($order, $postData)
    {

        $token = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_TOKEN, Mage::app()->getStore()
        );
        if (!$token) {
            Mage::log(
                "Vul uw JetVerzendt autorisatiegegevens correct in", null,
                'jetverzendt.log'
            );
            exit;
        }

        // check auto tracktrace mail option
        $autoTrackTrace = (Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_AUTO_TRACK_TRACE, Mage::app()->getStore()
        )) ? true : false;

        // format streetline data
        $street = $this->splitStreet(
            $order->getShippingAddress()->getData('street')
        );

        $shipmentData                   = array();
        $shipmentData['company_name']   = $order->getShippingAddress()
            ->getCompany();
        $shipmentData['street_line_1']  = $street[0];
        $shipmentData['number_line_1']  = $street[1];
        $shipmentData['zip_code']       = $order->getShippingAddress()
            ->getPostcode();
        $shipmentData['city']           = $order->getShippingAddress()->getCity();
        $shipmentData['country']        = $order->getShippingAddress()
            ->getCountry();
        $shipmentData['contact_person'] = $order->getShippingAddress()
                ->getFirstname() . ' ' . $order->getShippingAddress()
                ->getMiddlename() . ' '
            . $order->getShippingAddress()->getLastname();

        $userData              = $this->getUserDetails($order);
        $shipmentData['phone'] = $userData['telephone'];
        $shipmentData['email'] = $userData['email'];

        $shipmentData['pickup_date']  = (isset($postData['pickup_date'])
            && !empty($postData['pickup_date'])) ? date(
            'Y-m-d', strtotime($postData['pickup_date'])
        ) : Mage::getModel('core/date')->date('Y-m-d', strtotime("+1 day"));
        $shipmentData['product']      = (isset($postData['product'])
            && $postData['product']) ? $postData['product'] : '';
        $shipmentData['service']      = (isset($postData['service'])
            && $postData['service']) ? $postData['service'] : '';
        $shipmentData['product_type'] = '2';
        $shipmentData['amount']       = (isset($postData['amount'])
            && $postData['amount']) ? $postData['amount'] : '';

        if (isset($postData['reference']) && !empty($postData['reference'])) {
            $shipmentData['reference'] = $postData['reference'];
        } elseif (isset($postData['increment_id'])
            && !empty($postData['increment_id'])
        ) {
            $shipmentData['reference'] = $postData['increment_id'];
        } else {
            $shipmentData['reference'] = '';
        }
        $shipmentData['predict']                    = (isset($postData['predict'])
            && $postData['predict']) ? $postData['predict'] : '';
        $shipmentData['cod']                        = (isset($postData['cod'])
            && $postData['cod']) ? number_format($postData['cod'], 2, ',', '')
            : '';
        $shipmentData['saturday_delivery']
                                                    = (isset($postData['saturday_delivery'])
            && $postData['saturday_delivery']) ? 1 : 0;
        $shipmentData['weight']
                                                    = (isset($postData['weight'])
            && $postData['weight']) ? $postData['weight'] : '';
        $shipmentData['signature_required']
                                                    = (isset($postData['signature_required'])
            && $postData['signature_required']) ? 1 : 0;
        $shipmentData['not_by_neighbours']
                                                    = (isset($postData['not_by_neighbours'])
            && $postData['not_by_neighbours']) ? 1 : 0;
        $shipmentData['evening']
                                                    = (isset($postData['evening'])
            && $postData['evening']) ? 1 : 0;
        $shipmentData['extra_insurance']
                                                    = (isset($postData['extra_insurance'])
            && $postData['extra_insurance']) ? number_format(
            $postData['extra_insurance'], 2, ',', ''
        ) : '';
        $shipmentData['insurance']
                                                    = (isset($postData['insurance'])
            && $postData['insurance']) ? number_format(
            $postData['insurance'], 2, ',', ''
        ) : '';
        $shipmentData['parcel_shop_id']
                                                    = (isset($postData['parcel_shop_id'])
            && $postData['parcel_shop_id']) ? $postData['parcel_shop_id'] : '';
        $shipmentData['send_track_and_trace_email'] = $autoTrackTrace;

        $apiStatus = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LIVE_STATUS, Mage::app()->getStore()
        );
        if ($apiStatus) {
            $ch = curl_init(
                'https://portal.jetverzendt.nl/api/v2/shipment?api_token='
                . $token
            );
        } else {
            $ch = curl_init(
                'http://testportal.jetverzendt.nl/api/v2/shipment?api_token='
                . $token
            );
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($shipmentData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Content-Length: ' . strlen(json_encode($shipmentData))
            )
        );

        return json_decode(curl_exec($ch));

    }

    /**
     * Retrieves user details such as email address and telephone number from order
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    private function getUserDetails(Mage_Sales_Model_Order $order)
    {
        $results      = array();
        $emailAddress = $order->getData('customer_email');
        $telephone    = null;

        foreach ($order->getAddressesCollection() as $address) {
            $addressType = $address->getAddressType();
            if (($addressType == 'shipping' || $addressType == 'billing')
                && !$address->isDeleted()
            ) {
                if ($emailAddress == null) {
                    $emailAddress = $address->getData('email');
                }
                if ($telephone == null) {
                    $telephone = $address->getData('telephone');
                }
            }
        }
        $results['email']     = $emailAddress;
        $results['telephone'] = $telephone;

        return $results;
    }


    /**
     * Retrieve order information from the Jet Verzendt server
     *
     * @param $order
     */
    public function retrieveOrderFromJetVerzendt($order)
    {

        // get jet verzendt authorisation data
        $token = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_TOKEN, Mage::app()->getStore()
        );
        if (!$token) {
            Mage::log(
                "Vul uw JetVerzendt autorisatiegegevens correct in", null,
                'jetverzendt.log'
            );
            exit;
        }
        $apiStatus = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LIVE_STATUS, Mage::app()->getStore()
        );

        if ($apiStatus) {
            $ch = curl_init(
                'https://portal.jetverzendt.nl/api/v2/shipment/'
                . $order->getJetShipmentId() . '?api_token=' . $token
            );
        } else {
            $ch = curl_init(
                'http://testportal.jetverzendt.nl/api/v2/shipment/'
                . $order->getJetShipmentId() . '?api_token=' . $token
            );
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            )
        );

        return json_decode(curl_exec($ch));
    }


    /**
     * Retrieve labels from the Jet Verzendt server.
     *
     * @param $jetIds : array with Jet Verzendt ids
     */
    public function retrieveLabelsFromJetVerzendt($jetIds)
    {

        // get user settings
        $token = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_TOKEN, Mage::app()->getStore()
        );
        if (!$token) {
            Mage::log(
                "Vul uw JetVerzendt autorisatiegegevens correct in", null,
                'jetverzendt.log'
            );
            exit;
        }


        // get printer settings
        $printerMethod = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_PRINTER_METHODS, Mage::app()->getStore()
        );
        $printerLabel  = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_PRINTER_LABEL, Mage::app()->getStore()
        );
        if ($printerMethod == 'bat') {
            $fileName    = 'Verzendlabels Jet Verzendt.bat';
            $contentType = 'Content-type:application/txt';
        } elseif ($printerMethod == 'zpl') {
            $fileName    = 'Verzendlabels Jet Verzendt.zpl';
            $contentType = 'Content-type:application/txt';
        } else {
            $fileName    = 'Verzendlabels Jet Verzendt.pdf';
            $contentType = 'Content-type:application/pdf';
        }

        // set post url
        $apiStatus = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LIVE_STATUS, Mage::app()->getStore()
        );
        if ($apiStatus) {
            $ch = curl_init(
                'https://portal.jetverzendt.nl/api/v2/label?api_token=' . $token
            );
        } else {
            $ch = curl_init(
                'http://testportal.jetverzendt.nl/api/v2/label?api_token='
                . $token
            );
        }


        $labelData = json_encode(
            array(
                'shipments' => $jetIds,
                'label' => array(
                    'type' => strtoupper($printerMethod),
                    'size' => strtoupper($printerLabel)
                )
            )
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $labelData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Content-Length: ' . strlen($labelData)
            )
        );

        $result = json_decode(curl_exec($ch));

        if (isset($result->labels)) {
            header($contentType);
            header(
                'Content-Disposition: attachment; filename="' . $fileName . '"'
            );
            echo base64_decode($result->labels);
            exit;
        } else {
            die("Ophalen labels is helaas mislukt");
        }
    }


    /**
     * Create an shipment from an order
     *
     * @param $orderId
     *
     * @return bool
     */
    public function createShipmentFromOrder($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        if ($order->canShip()) {
            $shipment   = new Mage_Sales_Model_Order_Shipment_Api();
            $shipmentId = $shipment->create($order->getIncrementId());


            if (is_numeric($shipmentId) && $shipmentId > 0) {
                return true;
            } else {
                return false;
            }
        }

        return false;

    }


    public function changeOrderStatusToComplete($orderId)
    {
        if (Mage::helper('jetverzendt_shipping')->isLastMileStatusComplete()) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $order->setData('state', "complete");
            $order->setStatus("complete");
            $history = $order->addStatusHistoryComment(
                'Orderstatus is automatisch op compleet gezet.', false
            );
            $history->setIsCustomerNotified(null);
            $order->save();

            return true;
        } else {
            return false;
        }
    }


    /**
     * Get parcel shop data form the portal
     *
     * @param $address
     *
     * @return mixed
     */
    public function getParcelShops($address)
    {
        // get user settings
        $token = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_TOKEN, Mage::app()->getStore()
        );
        if (!$token) {
            Mage::log(
                "Vul uw JetVerzendt autorisatiegegevens correct in", null,
                'jetverzendt.log'
            );
            exit;
        }
        // set post url
        $apiStatus = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LIVE_STATUS, Mage::app()->getStore()
        );
        if ($apiStatus) {
            $ch = curl_init(
                'https://portal.jetverzendt.nl/api/v2/parcel-shop/search?api_token='
                . $token
            );
        } else {
            $ch = curl_init(
                'http://testportal.jetverzendt.nl/api/v2/parcel-shop/search?api_token='
                . $token
            );
        }

        $products                      = array();
        $isLastmileDpdParcelshopEnable = Mage::helper('jetverzendt_shipping')
            ->isLastmileDpdParcelshopEnable();
        $isLastmileDhlParcelshopEnable = Mage::helper('jetverzendt_shipping')
            ->isLastmileDhlParcelshopEnable();
        if ($isLastmileDhlParcelshopEnable) {
            $products[] = 'DHL';
        }
        if ($isLastmileDpdParcelshopEnable) {
            $products[] = 'DPD';
        }


        // create data
        $search_data = json_encode(
            array(
                'number_line_1' => $address['number'],
                'zip_code' => $address['zip_code'],
                'country' => $address['country'],
                'products' => $products
            )
        );


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $search_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Content-Length: ' . strlen($search_data)
            )
        );

        return json_decode(curl_exec($ch));


    }


    /**
     * Get deliver dates
     *
     * @param $address
     *
     * @return mixed
     */
    public function getDeliverDates($address)
    {
        // get user settings
        $token = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_TOKEN, Mage::app()->getStore()
        );
        if (!$token) {
            Mage::log(
                "Vul uw JetVerzendt autorisatiegegevens correct in", null,
                'jetverzendt.log'
            );
            exit;
        }

        // set post url
        $apiStatus = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LIVE_STATUS, Mage::app()->getStore()
        );
        if ($apiStatus) {
            $ch = curl_init(
                'https://portal.jetverzendt.nl/api/v2/delivery-schedule/search?api_token='
                . $token
            );
        } else {
            $ch = curl_init(
                'http://testportal.jetverzendt.nl/api/v2/delivery-schedule/search?api_token='
                . $token
            );
        }

        // create data
        $search_data = json_encode(
            [
                'zip_code' => $address->getPostcode(),
                'products' => 'DHL'
            ]
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $search_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Content-Length: ' . strlen($search_data)
            )
        );

        //return json_decode(curl_exec($ch));

        $result = json_decode(curl_exec($ch));

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 422) {
            return false;
        } else {
            return $result;
        }

    }


    /**
     * @return int|mixed
     */
    public function getLastmilePriceDhlEvening()
    {
        $price = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_PRICE_DHL_EVENING,
            Mage::app()->getStore()
        );
        if (isset($price)) {
            return str_replace(',', '.', $price);
        }

        return 0;
    }


    /**
     * @return int|mixed
     */
    public function getLastmilePriceDpdSaterday()
    {
        $price = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_SATERDAY_DPD_PRICE,
            Mage::app()->getStore()
        );
        if (isset($price)) {
            return str_replace(',', '.', $price);
        }

        return 0;
    }

    /**
     * @return int|mixed
     */
    public function getLastmilePriceDhlParcelshop()
    {
        $price = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_PRICE_DHL_PARCELSHOP,
            Mage::app()->getStore()
        );
        if (isset($price)) {
            return str_replace(',', '.', $price);
        }

        return 0;
    }

    /**
     * @return int|mixed
     */
    public function getLastmilePriceDpdParcelshop()
    {
        $price = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_PRICE_DPD_PARCELSHOP,
            Mage::app()->getStore()
        );
        if (isset($price)) {
            return str_replace(',', '.', $price);
        }

        return 0;
    }

    /**
     * @return int|mixed
     */
    public function getLastmilePriceFadello()
    {
        $price = Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_FADELLO_PRICE,
            Mage::app()->getStore()
        );
        if (isset($price)) {
            return str_replace(',', '.', $price);
        }

        return 0;
    }

    /**
     * @return int|mixed
     */
    public function getLastmileGoogleMapsKey()
    {
        return Mage::getStoreConfig(
            self::STORE_CONFIG_PATH_LASTMILE_GOOGLE_MAPS_KEY,
            Mage::app()->getStore()
        );
    }

    /**
     * @return string
     */
    public function checkValidWeightsByKg($weights = 20)
    {
        $cart = Mage::getModel('checkout/cart')->getQuote();
        foreach ($cart->getAllItems() as $item) {
            if ($item->getProduct()->getWeight() > $weights) { // kg
                return false;
            }
        }

        return true;
    }


    /**
     * Check if a date is a dutch holiday day
     *
     * @param $date
     *
     * @return bool
     */
    public function isDutchPublicHoliday($date)
    {
        $holidayDays = array();
        $date        = date('Ymd', strtotime($date));
        $year        = date('Y', strtotime($date));

        $nieuwjaar      = new \DateTime($year . "-01-01");
        $koningsdag     = new \DateTime($year . "-04-27");
        $kerstdag       = new \DateTime($year . "-12-25");
        $tweedeKerstdag = new \DateTime($year . "-12-26");

        $paasdag = new \DateTime();
        $paasdag->setTimestamp(easter_date($year));

        $tweedePaasdag = clone $paasdag;
        $tweedePaasdag->add(new \DateInterVal('P1D')); // 1 dag na pasen
        $hemelvaart = clone $paasdag;
        $hemelvaart->add(new \DateInterVal('P39D')); // 39 dagen na pasen
        $pinksteren = clone $hemelvaart;
        $pinksteren->add(new \DateInterVal('P10D')); // 10 dagen na hemelvaart
        $tweedePinksterdag = clone $pinksteren;
        $tweedePinksterdag->add(
            new \DateInterVal('P1D')
        ); // 1 dag na pinksteren

        $holidayDays[] = $nieuwjaar->format('Ymd');
        $holidayDays[] = $paasdag->format('Ymd');
        $holidayDays[] = $tweedePaasdag->format('Ymd');
        $holidayDays[] = $koningsdag->format('Ymd');
        $holidayDays[] = $hemelvaart->format('Ymd');
        $holidayDays[] = $pinksteren->format('Ymd');
        $holidayDays[] = $tweedePinksterdag->format('Ymd');
        $holidayDays[] = $kerstdag->format('Ymd');
        $holidayDays[] = $tweedeKerstdag->format('Ymd');

        if (in_array($date, $holidayDays)) {
            return true;
        }

        return false;

    }


    /**
     * Get country from current quote shipping address
     *
     * @return mixed
     */
    public function getQuoteShippingAddressCountry()
    {
        return Mage::getModel('checkout/session')->getQuote()
            ->getShippingAddress()->getCountry();

    }


}


