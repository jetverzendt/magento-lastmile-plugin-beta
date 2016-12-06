<?php

/**
 * Class JetVerzendt_Shipping_Model_Adminhtml_Observer
 */
class JetVerzendt_Shipping_Model_Adminhtml_Observer
{

    /**
     * Get shipment data for admin shipment grid
     *
     * @param $observer
     */
    public function salesShipmentGridCollectionLoadBefore($observer)
    {
        $collection = $observer->getOrderShipmentGridCollection();
        $select     = $collection->getSelect();
        $select->joinLeft(
            array('shipment' => $collection->getTable(
                'jetverzendt_shipping/shipment'
            )), 'shipment.mage_order_id = main_table.order_id',
            array('pickup_date'   => 'shipment.pickup_date',
                  'label_printed' => 'shipment.label_printed')
        );
        $select->joinLeft(
            array('package' => $collection->getTable(
                'jetverzendt_shipping/package'
            )), 'package.mage_order_id = main_table.order_id',
            array('tracktrace' => new Zend_Db_Expr(
                'group_concat( "<a href=", `package`.track_trace_url, ">", CONCAT(`package`.track_trace_key), "</a>" SEPARATOR "<br/>")'
            ))
        );
        $select->joinLeft(
            array('orders' => $collection->getTable(
                'sales/order'
            )), 'main_table.order_id = orders.entity_id',
            array('jet_last_mile' => 'orders.jet_last_mile')
        );
        $select->group('main_table.entity_id');
        //die((string)$select);
    }


    /**
     * Get shipment data for admin order grid
     *
     * @param $observer
     */
    public function salesOrderGridCollectionLoadBefore($observer)
    {
        $collection = $observer->getOrderGridCollection();
        $collection->getSelect()->join(
            array('orders' => $collection->getTable('sales/order')),
            'main_table.entity_id = orders.entity_id',
            array('jet_last_mile' => 'orders.jet_last_mile')
        );
    }


    /**
     * Add Jet Verzendt shipment columns to the admin shipment grid
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function appendCustomColumn(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        if ( ! isset($block)) {
            return $this;
        }
        if ($block->getType() == 'adminhtml/sales_shipment_grid') {

            if (Mage::helper('jetverzendt_shipping')->isLastmileEnabled()) {
                $block->addColumnAfter(
                    'jet_last_mile', array(
                    'header'       => 'Last Mile',
                    'type'         => 'jet_last_mile',
                    'index'        => 'jet_last_mile',
                    'width'        => '200px',
                    'string_limit' => '99999',
                    'renderer'     => 'JetVerzendt_Shipping_Block_Adminhtml_Widget_Grid_Column_Renderer_Lastmile'
                ), 'shipping_name'
                );
            }

            $block->addColumnAfter(
                'label_printed', array(
                'header'           => 'Labels geprint',
                'type'             => 'options',
                'width'            => '50px',
                'column_css_class' => 'jet_label_printed',
                'index'            => 'label_printed',
                'options'          => array('0' => 'Nee', '1' => 'Ja')
            ), 'jet_last_mile'
            );

            $block->addColumnAfter(
                'tracktrace', array(
                'header'       => 'Track & trace',
                'type'         => 'text',
                'index'        => 'tracktrace',
                'width'        => '115px',
                'string_limit' => '99999'
            ), 'label_printed'
            );
        }

        if ($block->getType() == 'adminhtml/sales_order_grid') {
            if (Mage::helper('jetverzendt_shipping')->isLastmileEnabled()) {
                $block->addColumnAfter(
                    'jet_last_mile', array(
                    'header'       => 'Last Mile',
                    'type'         => 'jet_last_mile',
                    'index'        => 'jet_last_mile',
                    'width'        => '200px',
                    'string_limit' => '99999',
                    'renderer'     => 'JetVerzendt_Shipping_Block_Adminhtml_Widget_Grid_Column_Renderer_Lastmile'
                ), 'shipping_name'
                );
            }
        }
    }


    /**
     * @param $observer
     */
    public function addShipmentsDropdownAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'sales_shipment'
        ) {
            $block->addItem(
                'jetverzendt', array(
                    'label' => 'Print Jet Verzendt labels',
                    'url'   => Mage::getModel('adminhtml/url')->getUrl(
                        'adminhtml/jetverzendt/printlabels'
                    )
                )
            );
        }
    }

    /**
     * @param $observer
     */
    public function addOrdersDropdownAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'sales_order'
        ) {
            $block->addItem(
                'jetverzendt', array(
                    'label' => 'Verstuur orders naar Jet Verzendt',
                    'url'   => Mage::getModel('adminhtml/url')->getUrl(
                        'adminhtml/jetverzendt/overview'
                    )
                )
            );
        }
    }

}
