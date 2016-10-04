<?php
/**
 *
 */


$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('jetverzendt_shipping/shipment')};

CREATE TABLE IF NOT EXISTS {$this->getTable('jetverzendt_shipping/shipment')} (
  `id` int(11) NOT NULL auto_increment,
  `mage_order_id` int(11) NOT NULL COMMENT 'magento order entity_id',
  `number_of_packages` int(11) DEFAULT NULL COMMENT 'number of packages',
  `pickup_date` datetime DEFAULT NULL,
  `label_printed` tinyint(1) NOT NULL DEFAULT '0',
  `jet_shipment_id` int(11) DEFAULT NULL COMMENT 'jetverzendt shipment id',
  `jet_updated_at` datetime DEFAULT NULL COMMENT 'updated_at value from jet verzendt portal',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jetverzendt_shipping_shipment';



-- DROP TABLE IF EXISTS {$this->getTable('jetverzendt_shipping/package')};
CREATE TABLE IF NOT EXISTS {$this->getTable('jetverzendt_shipping/package')} (
  `package_id` int(11) NOT NULL auto_increment COMMENT 'package_id',
  `mage_order_id` int(11) NOT NULL COMMENT 'magento order entity_id',
  `track_trace_key` varchar(100) NOT NULL,
  `track_trace_url` text NOT NULL,
  `shipment_method_name` varchar(255) NOT NULL,
  `shipment_method_class` varchar(255) NOT NULL,
  PRIMARY KEY (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jetverzendt_shipping_package';

");




$installer->endSetup();
