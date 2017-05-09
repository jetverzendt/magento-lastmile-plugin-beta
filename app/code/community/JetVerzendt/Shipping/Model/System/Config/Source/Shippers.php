<?php


class JetVerzendt_Shipping_Model_System_Config_Source_Shippers
{
    /**
     * Show the KeenDelivery Shippers
     *
     * @return array
     */
    public function toOptionArray()
    {


        $options = array();

        $options[] = array('value' => 'DHL', 'label' => 'DHL');
        $options[] = array('value' => 'DPD', 'label' => 'DPD');
        $options[] = array('value' => 'Fadello', 'label' => 'Same Day Delivery');
        $options[] = array('value' => 'NextDayPremium', 'label' => 'Next Day Premium');

        return $options;

    }

}