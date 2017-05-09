<?php


class JetVerzendt_Shipping_Model_System_Config_Source_Label
{
    /**
     * Options getter for KeenDelivery config page select box
     *
     * @return array
     */
    public function toOptionArray()
    {


        $options = array();

        $options[] = array('value' => 'default', 'label' => 'Standaard');
        $options[] = array('value' => 'A6', 'label' => '4 labels combineren tot A4');

        return $options;

    }

}