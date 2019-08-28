<?php


class JetVerzendt_Shipping_Model_System_Config_Source_Printer
{
    /**
     * Options getter for KeenDelivery config page select box
     *
     * @return array
     */
    public function toOptionArray()
    {


        $options = array();

        $options[] = array('value' => 'pdf', 'label' => 'PDF');
        $options[] = array('value' => 'bat', 'label' => 'BAT (werkt alleen met DHL)');
        $options[] = array('value' => 'zpl', 'label' => 'ZPL (werkt alleen met DHL)');

        return $options;

    }

}