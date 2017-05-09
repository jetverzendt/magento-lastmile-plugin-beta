<?php


class JetVerzendt_Shipping_Model_System_Config_Source_Carriers
{
	/**
	 * Options getter for KeenDelivery config page multiselect box
	 *
	 * @return array
	 */
	public function toOptionArray()
	{

		$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();

		$options = array();

		foreach($methods as $_ccode => $_carrier)
		{
			$_methodOptions = array();
			if($_methods = $_carrier->getAllowedMethods())
			{

				if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
					$_title = $_ccode;

				$options[] = array('value' => $_ccode, 'label' => $_title);
			}
		}


		return $options;

	}

}