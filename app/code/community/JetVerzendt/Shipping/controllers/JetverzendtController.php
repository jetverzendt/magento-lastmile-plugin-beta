<?php

/**
 * Class JetVerzendt_Shipping_ShippinglabelsController
 */
class JetVerzendt_Shipping_JetverzendtController extends Mage_Adminhtml_Controller_Action
{

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/shipment');
    }

    /**
     * Bulk update for multiple orders
     */
    public function updateordersAction()
    {


        // set variables
        $fromDate = Mage::getModel('core/date')->date('Y-m-d H:i:s', strtotime('-10 week'));
        $toDate = Mage::getModel('core/date')->date('Y-m-d H:i:s', strtotime('now'));

        // get orders
        $collection = Mage::getModel('jetverzendt_shipping/shipment')->getCollection();
        $collection->addFieldToFilter('jet_shipment_id', array('notnull' => true));
        $collection->addFieldToFilter('s_o.created_at', array('from' => $fromDate, 'to' => $toDate));
        $collection->addFieldToFilter(
            array('main_table.label_printed', 'main_table.jet_updated_at', 'j_p.track_trace_key'),
            array(
                array('like' => '0'),
                array('null' => true),
                array('null' => true)
            )
        );
        $collection->getSelect()->join(
            array(
                's_o' => Mage::getSingleton('core/resource')->getTableName(
                    'sales/order'
                )
            ),
            'main_table.mage_order_id = s_o.entity_id', array('s_o.created_at')
        );
        $collection->getSelect()->joinLeft(
            array(
                'j_p' => Mage::getSingleton('core/resource')->getTableName(
                    'jetverzendt_shipping/package'
                )
            ),
            'main_table.mage_order_id = j_p.mage_order_id', array('j_p.track_trace_key')
        );

        $collection->getSelect()->group('main_table.mage_order_id');


        // foreach orderdata
        foreach ($collection as $order) {
            $result = Mage::helper('jetverzendt_shipping')->retrieveOrderFromJetVerzendt($order);

            if (!$result) { // no server connection
                //Mage::log('Fout bij maken van portal', null, 'jetverzendt.log');

            } elseif ($result->track_and_trace && $result->shipment_method) { // if not empty track&trace and shipment method info

                $shipment = Mage::getModel('jetverzendt_shipping/shipment')->load($order->getJetShipmentId(), 'jet_shipment_id');


                if ($order->getMageOrderId() > 0) {
                    if ($result->updated_at != $shipment->getJetUpdatedAt()) { // an update!


                        // update shipment table
                        $shipment->setNumberOfPackages($result->data->amount)
                            ->setPickupDate($result->pickup_date)
                            ->setLabelPrinted(($result->status == 5) ? 1 : 0)
                            ->setJetUpdatedAt($result->updated_at)
                            ->save();


                        // delete current track/trace info for new update
                        $currentPackages = Mage::getModel('jetverzendt_shipping/package')->getCollection();
                        $currentPackages->addFieldToFilter('mage_order_id', $order->getMageOrderId());

                        foreach ($currentPackages as $pack) {
                            $pack->delete();
                        }

                        // insert track/trace and shipment info
                        foreach ($result->track_and_trace as $key => $url) {
                            Mage::getModel('jetverzendt_shipping/package')
                                ->setMageOrderId($order->getMageOrderId())
                                ->setTrackTraceKey($key)
                                ->setTrackTraceUrl($url)
                                ->setShipmentMethodName(
                                    $result->shipment_method->name
                                )
                                ->setShipmentMethodClass(
                                    $result->shipment_method->class
                                )
                                ->save();

                        }


                    }
                }
            }


        }
        return;
    }


    /**
     * Print KeenDelivery labels
     *
     * @throws Exception
     */
    public function printlabelsAction()
    {

        $jetIds = array();
        $ids = $this->getRequest()->getPost('shipment_ids');

        if (isset($ids) && is_array($ids)) {
            try {

                $collection = Mage::getModel('sales/order_shipment')->getCollection();
                $collection->addAttributeToFilter('j_s.jet_shipment_id', array('notnull' => true));
                $collection->addAttributeToFilter('entity_id', array('in' => $ids));
                $collection->getSelect()->join(
                    array('j_s' => Mage::getSingleton('core/resource')->getTableName('jetverzendt_shipping/shipment')),
                    'main_table.order_id = j_s.mage_order_id', array('j_s.*')
                );

                foreach ($collection as $order) {
                    $jetIds[] = $order->getJetShipmentId();

                    $shipment = Mage::getModel('jetverzendt_shipping/shipment')->load($order->getJetShipmentId(), 'jet_shipment_id');
                    $shipment->setLabelPrinted(1);
                    $shipment->setJetUpdatedAt(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
                    $shipment->save();
                }

                if (!empty($jetIds)) {
                    Mage::helper('jetverzendt_shipping')->retrieveLabelsFromJetVerzendt($jetIds);


                } else {
                    Mage::getSingleton('core/session')->addError('Helaas... Deze labels kunnen niet worden afgedrukt');
                    Mage::app()->getResponse()->setRedirect(
                        Mage::helper('adminhtml')->getUrl(
                            '*/sales_shipment'
                        )
                    )->sendResponse();
                }

            } catch (Exception $e) {
                $debugData['http_error'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                Mage::log($debugData, null, 'jetverzendt.log');
                throw $e;
            }
            return '';
        }

    }

    /**
     * Bulk order send to KeenDelivery
     */
    public function overviewAction()
    {

        $this->loadLayout();

        // get ids
        $ids = $this->getRequest()->getPost('order_ids');
        if (isset($ids) && is_array($ids)) {
            $collection = Mage::getModel('sales/order')->getCollection();
            $collection->addAttributeToFilter('entity_id', array('in' => $ids));
            $collection->setOrder('entity_id', 'DESC');

            // order overview form
            $block = $this->getLayout()->createBlock(
                'Mage_Core_Block_Template',
                'jet_verzendt_order_overview',
                array('template' => 'jetverzendt/sales/order/send_order_overview.phtml')
            )->setData('orders', $collection);

            $this->getLayout()->getBlock('content')->append($block);

            $this->renderLayout();

        }
    }


    /**
     * Save default order setting
     */
    public function defaultorderAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $postData = $this->getRequest()->getPost();
            if ($postData) {
                // save post in custom config
                Mage::getModel('core/config')->saveConfig('jetverzendt/default_shipment_form', serialize($postData));
                Mage::app()->getStore()->resetConfig();
            }

        }
        return '';
    }

    /**
     * Bulk order send action to KeenDelivery
     */
    public function sendordersAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $postData = $this->getRequest()->getPost();
            $defaultShipmentSettingTime = Mage::getStoreConfig('jetverzendt/default_shipment_form_time');
            $defaultShipmentSettingTime = (isset($defaultShipmentSettingTime) && $defaultShipmentSettingTime > 0) ? (int)$defaultShipmentSettingTime : 0;

            if (isset($postData['default_order']) && $postData['default_order'] == 1 && (time() - $defaultShipmentSettingTime) > 300) { // do not update within 5 minutes
                $postDataSettings = $postData;
                unset($postDataSettings['entity_id']);
                unset($postDataSettings['increment_id']);
                unset($postDataSettings['default_order']);
                unset($postDataSettings['form_key']);
                Mage::getModel('core/config')->saveConfig('jetverzendt/default_shipment_form', http_build_query($postDataSettings));
                Mage::getModel('core/config')->saveConfig('jetverzendt/default_shipment_form_time', time());
                Mage::app()->getStore()->resetConfig();
            }

            // get current; order ID
            $orderId = (isset($postData['entity_id']) && $postData['entity_id'] > 0) ? $postData['entity_id'] : "Ongeldige invoer";

            $order = Mage::getModel('sales/order')->load($orderId);


            $result = Mage::helper('jetverzendt_shipping')->sendOrdersToJetVerzendt($order, $postData);

            if (isset($result->shipment_id) && (int)$result->shipment_id > 0) { // request successful


                // create new shipment row in jetverzendt shipment table
                $new_shipment = Mage::getModel('jetverzendt_shipping/shipment');
                $new_shipment->setMageOrderId($orderId);
                $new_shipment->setNumberOfPackages($postData['amount']);
                $new_shipment->setPickupDate(
                    (isset($postData['pickup_date']) && !empty($postData['pickup_date'])) ? Mage::getModel('core/date')->date(
                        'Y-m-d', strtotime($postData['pickup_date'])
                    ) : Mage::getModel('core/date')->date('Y-m-d')
                );
                $new_shipment->setLabelPrinted(0);
                $new_shipment->setJetUpdatedAt(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
                $new_shipment->setJetShipmentId($result->shipment_id);
                $new_shipment->save();


                // delete current track/trace info for new update
                /*$currentPackages = Mage::getModel('jetverzendt_shipping/package')->getCollection();
                $currentPackages->addFieldToFilter('mage_order_id', $orderId);

                foreach ($currentPackages as $pack) {
                    $pack->delete();
                }*/

                // insert track/trace and shipment info
                foreach ($result->track_and_trace as $key => $url) {
                    if (isset($key) && isset($url)) {
                        // Store in Keen package table
                        Mage::getModel('jetverzendt_shipping/package')
                            ->setMageOrderId($orderId)
                            ->setTrackTraceKey($key)
                            ->setTrackTraceUrl($url)
                            ->setShipmentMethodName(
                                $result->shipment_method->name
                            )
                            ->setShipmentMethodClass(
                                $result->shipment_method->class
                            )
                            ->save();

                    }
                }


                $shipmentId = Mage::helper('jetverzendt_shipping')->createShipmentFromOrder($orderId);
                Mage::helper('jetverzendt_shipping')->changeOrderStatusToComplete($orderId);
                if ($shipmentId === false) {
                    $this->getResponse()->setBody("2");
                } else {
                    // new shipment created
                    $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId, 'increment_id');

                    if (!$shipment->getId()) {
                        $this->_fault('not_exists');
                    }


                    foreach ($result->track_and_trace as $key => $url) {
                        if (isset($key) && isset($url)) {

                            $track = Mage::getModel('sales/order_shipment_track')
                                ->setNumber($key)
                                ->setCarrierCode('custom')
                                ->setTitle($result->shipment_method->name);

                            $shipment->addTrack($track);

                            try {
                                $shipment->save();
                                $track->save();
                            } catch (Mage_Core_Exception $e) {
                                $this->_fault('data_invalid', $e->getMessage());
                            }
                        }
                    }

                    $this->getResponse()->setBody("1");
                }


            } else { // jet error

                if (!$result) { // no server connection
                    $error = 'Fout bij verbinding maken met KeenDelivery portal. Controleer uw instellingen.';
                } else {

                    // convert different array formats to a single array
                    $result = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($result)), 0);
                    $error = implode('<br/> -', $result);
                }
                $this->getResponse()->setBody($error);
            }
            return '';
        }
    }


}