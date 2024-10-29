/*****************************************************
Start: Missing Tables in M1 DB backup
*****************************************************/
/*
CREATE TABLE IF NOT EXISTS `sales_flat_quote` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store Id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Updated At',
  `converted_at` timestamp NULL DEFAULT NULL COMMENT 'Converted At',
  `is_active` smallint(5) unsigned DEFAULT '1' COMMENT 'Is Active',
  `is_virtual` smallint(5) unsigned DEFAULT '0' COMMENT 'Is Virtual',
  `is_multi_shipping` smallint(5) unsigned DEFAULT '0' COMMENT 'Is Multi Shipping',
  `items_count` int(10) unsigned DEFAULT '0' COMMENT 'Items Count',
  `items_qty` decimal(12,4) DEFAULT '0.0000' COMMENT 'Items Qty',
  `orig_order_id` int(10) unsigned DEFAULT '0' COMMENT 'Orig Order Id',
  `store_to_base_rate` decimal(12,4) DEFAULT '0.0000' COMMENT 'Store To Base Rate',
  `store_to_quote_rate` decimal(12,4) DEFAULT '0.0000' COMMENT 'Store To Quote Rate',
  `base_currency_code` varchar(255) DEFAULT NULL COMMENT 'Base Currency Code',
  `store_currency_code` varchar(255) DEFAULT NULL COMMENT 'Store Currency Code',
  `quote_currency_code` varchar(255) DEFAULT NULL COMMENT 'Quote Currency Code',
  `grand_total` decimal(12,4) DEFAULT '0.0000' COMMENT 'Grand Total',
  `base_grand_total` decimal(12,4) DEFAULT '0.0000' COMMENT 'Base Grand Total',
  `checkout_method` varchar(255) DEFAULT NULL COMMENT 'Checkout Method',
  `customer_id` int(10) unsigned DEFAULT '0' COMMENT 'Customer Id',
  `customer_tax_class_id` int(10) unsigned DEFAULT '0' COMMENT 'Customer Tax Class Id',
  `customer_group_id` int(10) unsigned DEFAULT '0' COMMENT 'Customer Group Id',
  `customer_email` varchar(255) DEFAULT NULL COMMENT 'Customer Email',
  `customer_prefix` varchar(40) DEFAULT NULL COMMENT 'Customer Prefix',
  `customer_firstname` varchar(255) DEFAULT NULL COMMENT 'Customer Firstname',
  `customer_middlename` varchar(40) DEFAULT NULL COMMENT 'Customer Middlename',
  `customer_lastname` varchar(255) DEFAULT NULL COMMENT 'Customer Lastname',
  `customer_suffix` varchar(40) DEFAULT NULL COMMENT 'Customer Suffix',
  `customer_dob` datetime DEFAULT NULL COMMENT 'Customer Dob',
  `customer_note` varchar(255) DEFAULT NULL COMMENT 'Customer Note',
  `customer_note_notify` smallint(5) unsigned DEFAULT '1' COMMENT 'Customer Note Notify',
  `customer_is_guest` smallint(5) unsigned DEFAULT '0' COMMENT 'Customer Is Guest',
  `remote_ip` varchar(32) DEFAULT NULL COMMENT 'Remote Ip',
  `applied_rule_ids` varchar(255) DEFAULT NULL COMMENT 'Applied Rule Ids',
  `reserved_order_id` varchar(64) DEFAULT NULL COMMENT 'Reserved Order Id',
  `password_hash` varchar(255) DEFAULT NULL COMMENT 'Password Hash',
  `coupon_code` varchar(255) DEFAULT NULL COMMENT 'Coupon Code',
  `global_currency_code` varchar(255) DEFAULT NULL COMMENT 'Global Currency Code',
  `base_to_global_rate` decimal(12,4) DEFAULT NULL COMMENT 'Base To Global Rate',
  `base_to_quote_rate` decimal(12,4) DEFAULT NULL COMMENT 'Base To Quote Rate',
  `customer_taxvat` varchar(255) DEFAULT NULL COMMENT 'Customer Taxvat',
  `customer_gender` varchar(255) DEFAULT NULL COMMENT 'Customer Gender',
  `subtotal` decimal(12,4) DEFAULT NULL COMMENT 'Subtotal',
  `base_subtotal` decimal(12,4) DEFAULT NULL COMMENT 'Base Subtotal',
  `subtotal_with_discount` decimal(12,4) DEFAULT NULL COMMENT 'Subtotal With Discount',
  `base_subtotal_with_discount` decimal(12,4) DEFAULT NULL COMMENT 'Base Subtotal With Discount',
  `is_changed` int(10) unsigned DEFAULT NULL COMMENT 'Is Changed',
  `trigger_recollect` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Trigger Recollect',
  `ext_shipping_info` text COMMENT 'Ext Shipping Info',
  `gift_message_id` int(11) DEFAULT NULL COMMENT 'Gift Message Id',
  `is_persistent` smallint(5) unsigned DEFAULT '0' COMMENT 'Is Quote Persistent',
  `customer_balance_amount_used` decimal(12,4) DEFAULT NULL COMMENT 'Customer Balance Amount Used',
  `base_customer_bal_amount_used` decimal(12,4) DEFAULT NULL COMMENT 'Base Customer Bal Amount Used',
  `use_customer_balance` int(11) DEFAULT NULL COMMENT 'Use Customer Balance',
  `gift_cards` text COMMENT 'Gift Cards',
  `gift_cards_amount` decimal(12,4) DEFAULT NULL COMMENT 'Gift Cards Amount',
  `base_gift_cards_amount` decimal(12,4) DEFAULT NULL COMMENT 'Base Gift Cards Amount',
  `gift_cards_amount_used` decimal(12,4) DEFAULT NULL COMMENT 'Gift Cards Amount Used',
  `base_gift_cards_amount_used` decimal(12,4) DEFAULT NULL COMMENT 'Base Gift Cards Amount Used',
  `gw_id` int(11) DEFAULT NULL COMMENT 'Gw Id',
  `gw_allow_gift_receipt` int(11) DEFAULT NULL COMMENT 'Gw Allow Gift Receipt',
  `gw_add_card` int(11) DEFAULT NULL COMMENT 'Gw Add Card',
  `gw_base_price` decimal(12,4) DEFAULT NULL COMMENT 'Gw Base Price',
  `gw_price` decimal(12,4) DEFAULT NULL COMMENT 'Gw Price',
  `gw_items_base_price` decimal(12,4) DEFAULT NULL COMMENT 'Gw Items Base Price',
  `gw_items_price` decimal(12,4) DEFAULT NULL COMMENT 'Gw Items Price',
  `gw_card_base_price` decimal(12,4) DEFAULT NULL COMMENT 'Gw Card Base Price',
  `gw_card_price` decimal(12,4) DEFAULT NULL COMMENT 'Gw Card Price',
  `gw_base_tax_amount` decimal(12,4) DEFAULT NULL COMMENT 'Gw Base Tax Amount',
  `gw_tax_amount` decimal(12,4) DEFAULT NULL COMMENT 'Gw Tax Amount',
  `gw_items_base_tax_amount` decimal(12,4) DEFAULT NULL COMMENT 'Gw Items Base Tax Amount',
  `gw_items_tax_amount` decimal(12,4) DEFAULT NULL COMMENT 'Gw Items Tax Amount',
  `gw_card_base_tax_amount` decimal(12,4) DEFAULT NULL COMMENT 'Gw Card Base Tax Amount',
  `gw_card_tax_amount` decimal(12,4) DEFAULT NULL COMMENT 'Gw Card Tax Amount',
  `use_reward_points` int(11) DEFAULT NULL COMMENT 'Use Reward Points',
  `reward_points_balance` int(11) DEFAULT NULL COMMENT 'Reward Points Balance',
  `base_reward_currency_amount` decimal(12,4) DEFAULT NULL COMMENT 'Base Reward Currency Amount',
  `reward_currency_amount` decimal(12,4) DEFAULT NULL COMMENT 'Reward Currency Amount',
  `quick_ship` smallint(6) DEFAULT NULL COMMENT 'Quick Ship',
  PRIMARY KEY (`entity_id`),
  KEY `IDX_SALES_FLAT_QUOTE_CUSTOMER_ID_STORE_ID_IS_ACTIVE` (`customer_id`,`store_id`,`is_active`),
  KEY `IDX_SALES_FLAT_QUOTE_STORE_ID` (`store_id`),
  CONSTRAINT `FK_SALES_FLAT_QUOTE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=202749 DEFAULT CHARSET=utf8 COMMENT='Sales Flat Quote';
*/

/*
CREATE TABLE IF NOT EXISTS `log_visitor` (
  `visitor_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Visitor ID',
  `session_id` VARCHAR(64) DEFAULT NULL COMMENT 'Session ID',
  `first_visit_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'First Visit Time',
  `last_visit_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Last Visit Time',
  `last_url_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Last URL ID',
  `store_id` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'Store ID',
  PRIMARY KEY (`visitor_id`)
) ENGINE=INNODB AUTO_INCREMENT=23316457 DEFAULT CHARSET=utf8 COMMENT='Log Visitors Table';
*/

/*
CREATE TABLE IF NOT EXISTS `report_event` (
  `event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Event Id',
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Logged At',
  `event_type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Event Type Id',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Object Id',
  `subject_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Subject Id',
  `subtype` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Subtype',
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store Id',
  PRIMARY KEY (`event_id`),
  KEY `IDX_REPORT_EVENT_EVENT_TYPE_ID` (`event_type_id`),
  KEY `IDX_REPORT_EVENT_SUBJECT_ID` (`subject_id`),
  KEY `IDX_REPORT_EVENT_OBJECT_ID` (`object_id`),
  KEY `IDX_REPORT_EVENT_SUBTYPE` (`subtype`),
  KEY `IDX_REPORT_EVENT_STORE_ID` (`store_id`),
  CONSTRAINT `FK_REPORT_EVENT_EVENT_TYPE_ID_REPORT_EVENT_TYPES_EVENT_TYPE_ID` FOREIGN KEY (`event_type_id`) REFERENCES `report_event_types` (`event_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_REPORT_EVENT_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10461159 DEFAULT CHARSET=utf8 COMMENT='Reports Event Table';
*/
/*****************************************************
End: Missing Tables in M1 DB backup
*****************************************************/

/***********************************************/
SET FOREIGN_KEY_CHECKS = 0;
/***********************************************/

/* Start: Truncate the tables for which data is not required */
TRUNCATE TABLE catalog_category_flat_store_1;
TRUNCATE TABLE catalog_category_flat_store_2;
TRUNCATE TABLE catalog_product_flat_1;
TRUNCATE TABLE catalog_product_flat_2;
TRUNCATE TABLE catalogsearch_fulltext;
TRUNCATE TABLE catalogsearch_query;
TRUNCATE TABLE catalogsearch_recommendations;
TRUNCATE TABLE catalogsearch_result;
TRUNCATE TABLE dataflow_batch;
TRUNCATE TABLE dataflow_batch_export;
TRUNCATE TABLE dataflow_batch_import;
TRUNCATE TABLE dataflow_import_data;
TRUNCATE TABLE dataflow_profile;
TRUNCATE TABLE dataflow_profile_history;
TRUNCATE TABLE dataflow_session;
TRUNCATE TABLE enterprise_customer_sales_flat_order;
TRUNCATE TABLE enterprise_customer_sales_flat_order_address;
TRUNCATE TABLE enterprise_customer_sales_flat_quote;
TRUNCATE TABLE enterprise_customer_sales_flat_quote_address;
TRUNCATE TABLE enterprise_logging_event;
TRUNCATE TABLE enterprise_logging_event_changes;
TRUNCATE TABLE log_customer;
TRUNCATE TABLE log_quote;
TRUNCATE TABLE log_summary;
TRUNCATE TABLE log_summary_type;
TRUNCATE TABLE log_url;
TRUNCATE TABLE log_url_info;
TRUNCATE TABLE log_visitor;
TRUNCATE TABLE log_visitor_info;
TRUNCATE TABLE log_visitor_online;
-- TRUNCATE TABLE oauth_consumer;
-- TRUNCATE TABLE oauth_nonce;
-- TRUNCATE TABLE oauth_token;
TRUNCATE TABLE report_compared_product_index;
TRUNCATE TABLE report_event;
-- TRUNCATE TABLE report_event_types;
TRUNCATE TABLE report_viewed_product_aggregated_daily;
TRUNCATE TABLE report_viewed_product_aggregated_monthly;
TRUNCATE TABLE report_viewed_product_aggregated_yearly;
TRUNCATE TABLE report_viewed_product_index;
TRUNCATE TABLE sales_bestsellers_aggregated_daily;
TRUNCATE TABLE sales_bestsellers_aggregated_monthly;
TRUNCATE TABLE sales_bestsellers_aggregated_yearly;
TRUNCATE TABLE sales_flat_order;
TRUNCATE TABLE sales_flat_order_address;
TRUNCATE TABLE sales_flat_order_grid;
TRUNCATE TABLE sales_flat_order_item;
TRUNCATE TABLE sales_flat_order_payment;
TRUNCATE TABLE sales_flat_order_status_history;
TRUNCATE TABLE sales_flat_quote;
TRUNCATE TABLE sales_flat_quote_address;
TRUNCATE TABLE sales_flat_quote_address_item;
TRUNCATE TABLE sales_flat_quote_item;
TRUNCATE TABLE sales_flat_quote_item_option;
TRUNCATE TABLE sales_flat_quote_payment;
TRUNCATE TABLE sales_flat_quote_shipping_rate;
TRUNCATE TABLE sales_flat_shipment;
TRUNCATE TABLE sales_flat_shipment_comment;
TRUNCATE TABLE sales_flat_shipment_grid;
TRUNCATE TABLE sales_flat_shipment_item;
TRUNCATE TABLE sales_flat_shipment_track;
TRUNCATE TABLE sales_invoiced_aggregated;
TRUNCATE TABLE sales_invoiced_aggregated_order;
TRUNCATE TABLE sales_order_aggregated_created;
TRUNCATE TABLE sales_order_aggregated_updated;
TRUNCATE TABLE sales_order_tax;
TRUNCATE TABLE sales_order_tax_item;
TRUNCATE TABLE sales_payment_transaction;
TRUNCATE TABLE sales_recurring_profile;
TRUNCATE TABLE sales_recurring_profile_order;
TRUNCATE TABLE sales_refunded_aggregated;
TRUNCATE TABLE sales_refunded_aggregated_order;
TRUNCATE TABLE sales_shipping_aggregated;
TRUNCATE TABLE sales_shipping_aggregated_order;
TRUNCATE TABLE captcha_log;
TRUNCATE TABLE cron_schedule;
TRUNCATE TABLE sendfriend_log;
TRUNCATE TABLE widget_instance;
TRUNCATE TABLE widget_instance_page;
TRUNCATE TABLE widget_instance_page_layout;
TRUNCATE TABLE enterprise_logging_event;
TRUNCATE TABLE enterprise_logging_event_changes;
TRUNCATE TABLE importexport_importdata;
TRUNCATE TABLE catalog_compare_item;
/* End: Truncate the tables for which data is not required */

ALTER TABLE `xtento_orderexport_destination` MODIFY last_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `xtento_orderexport_destination` MODIFY ftp_type VARCHAR(50);

ALTER TABLE xtento_orderexport_log MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE xtento_orderexport_log MODIFY log_id INT UNSIGNED NOT NULL;

ALTER TABLE xtento_orderexport_profile MODIFY profile_id INT UNSIGNED NOT NULL;

/***********************************************/
SET FOREIGN_KEY_CHECKS = 1;
/***********************************************/

/* Start - Delete Customers Customer store which is not required */
DELETE FROM `core_store` WHERE `code` = "cc";

DELETE FROM `core_config_data` WHERE scope = "websites" AND scope_id NOT IN (0, 1);
DELETE FROM `core_config_data` WHERE scope = "stores" AND scope_id NOT IN  (0, 1);
/* End - Delete Customers Customer store which is not required */

/* Start - Delete orphan values from eav_entity_attribute */
DELETE eea FROM eav_entity_attribute AS eea
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = eea.attribute_id
WHERE ea.attribute_id IS NULL;
/* End - Delete orphan values from eav_entity_attribute */


/* Start - Category: To delete orphan values */
DELETE CCEI FROM catalog_category_entity_int CCEI
LEFT JOIN catalog_category_entity CCE ON CCE.entity_id=CCEI.entity_id
WHERE CCE.entity_id IS NULL;

DELETE CCEV FROM catalog_category_entity_varchar CCEV
LEFT JOIN catalog_category_entity CCE ON CCE.entity_id=CCEV.entity_id
WHERE CCE.entity_id IS NULL;

DELETE CCED FROM catalog_category_entity_decimal CCED
LEFT JOIN catalog_category_entity CCE ON CCE.entity_id=CCED.entity_id
WHERE CCE.entity_id IS NULL;

DELETE CCET FROM catalog_category_entity_text CCET
LEFT JOIN catalog_category_entity CCE ON CCE.entity_id=CCET.entity_id
WHERE CCE.entity_id IS NULL;

DELETE CCED FROM catalog_category_entity_datetime CCED
LEFT JOIN catalog_category_entity CCE ON CCE.entity_id=CCED.entity_id
WHERE CCE.entity_id IS NULL;

DELETE ccei FROM `catalog_category_entity_int` AS ccei
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = ccei.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE ccedt FROM `catalog_category_entity_datetime` AS ccedt
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = ccedt.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE cced FROM `catalog_category_entity_decimal` AS cced
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = cced.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE ccet FROM `catalog_category_entity_text` AS ccet
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = ccet.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE ccev FROM `catalog_category_entity_varchar` AS ccev
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = ccev.attribute_id
WHERE ea.attribute_id IS NULL;
/* End - Category: To delete orphan values */


/* Start - Product: To delete orphan values */
DELETE FROM catalog_product_entity WHERE sku IS NULL;

DELETE CPEI FROM catalog_product_entity_int CPEI
LEFT JOIN catalog_product_entity CPE ON CPE.entity_id=CPEI.entity_id
WHERE CPE.sku IS NULL;

DELETE CPEV FROM catalog_product_entity_varchar CPEV
LEFT JOIN catalog_product_entity CPE ON CPE.entity_id=CPEV.entity_id
WHERE CPE.sku IS NULL;

DELETE CPED FROM catalog_product_entity_decimal CPED
LEFT JOIN catalog_product_entity CPE ON CPE.entity_id=CPED.entity_id
WHERE CPE.sku IS NULL;

DELETE CPET FROM catalog_product_entity_text CPET
LEFT JOIN catalog_product_entity CPE ON CPE.entity_id=CPET.entity_id
WHERE CPE.sku IS NULL;

DELETE CPED FROM catalog_product_entity_datetime CPED
LEFT JOIN catalog_product_entity CPE ON CPE.entity_id=CPED.entity_id
WHERE CPE.sku IS NULL;

DELETE CPEM FROM catalog_product_entity_media_gallery CPEM
LEFT JOIN catalog_product_entity CPE ON CPE.entity_id=CPEM.entity_id
WHERE CPE.sku IS NULL;

DELETE CPEMGV FROM `catalog_product_entity_media_gallery_value` CPEMGV
LEFT JOIN catalog_product_entity_media_gallery CPEMG ON CPEMG.value_id=CPEMGV.value_id
WHERE CPEMG.value_id IS NULL;

DELETE cpei FROM catalog_product_entity_int AS cpei
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = cpei.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE cpedt FROM catalog_product_entity_datetime AS cpedt
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = cpedt.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE cped FROM catalog_product_entity_decimal AS cped
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = cped.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE cped FROM catalog_product_entity_decimal AS cped
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = cped.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE cpet FROM `catalog_product_entity_text` AS cpet
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = cpet.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE cpev FROM `catalog_product_entity_varchar` AS cpev
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = cpev.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE cpemg FROM `catalog_product_entity_media_gallery` AS cpemg
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = cpemg.attribute_id
WHERE ea.attribute_id IS NULL;
/* End - Product: To delete orphan values */


/** Start - Delete orphan values in catalog_eav_attribute tables **/
DELETE cea FROM catalog_eav_attribute AS cea
LEFT JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
WHERE ea.attribute_id IS NULL;

DELETE eaov FROM eav_attribute_option_value AS eaov
LEFT JOIN eav_attribute_option AS eao ON eao.option_id = eaov.option_id
WHERE eao.option_id IS NULL;
/** End - Delete orphan values in catalog_eav_attribute tables **/


/** Start - Delete orphan values from grandriver_cc_wishlist_sharing **/
DELETE gcws FROM `grandriver_cc_wishlist_sharing` AS gcws
LEFT JOIN customer_entity AS ce ON ce.entity_id = gcws.cc_shared_with
WHERE ce.entity_id IS NULL;
/** End - Delete orphan values from grandriver_cc_wishlist_sharing **/


/** Start - Duplicate customer email **/
UPDATE customer_entity AS T1,
      (SELECT MAX(entity_id) AS entity_id
        FROM customer_entity
        GROUP BY email HAVING COUNT(*) > 1) AS T2
  SET T1.email = CONCAT("duplicate", T1.email)
  WHERE T1.entity_id = T2.entity_id;

-- SELECT * FROM customer_entity WHERE email LIKE "%laronsky25@gmail.com%";
UPDATE customer_entity SET email = CONCAT("duplicate", email) WHERE entity_id = 16567;
UPDATE customer_entity SET email = CONCAT("duplicate", email) WHERE entity_id = 16568;
/** End - Duplicate customer email **/


/**
DELTA MIGRATION WISHLIST
 */
/** Start - Move updated and new rows from wishlist table in M1 database to M2 database **/
/** PLEASE CHANGE DATABASE NAMES AND UPDATED DATE IN QUERY **/
INSERT INTO `m2_uat_migrated`.`wishlist`
SELECT ow.*,'' AS collaboration_ids FROM `wam1_nonprod_delta`.`wishlist` AS ow
INNER JOIN `m2_uat_migrated`.`customer_entity` AS customer ON customer.entity_id = ow.customer_id
WHERE DATE(ow.updated_at) > '2020-09-04 23:14:50'
ON DUPLICATE KEY UPDATE `customer_id` = ow.`customer_id` , shared = ow.shared,sharing_code=ow.sharing_code,updated_at=ow.updated_at,`name`=ow.`name`,visibility=ow.visibility;
/** End - Move updated and new rows from wishlist table in M1 database to M2 database **/

/** Start - Move updated and new rows from wishlist_item table in M1 database to M2 database **/
/** PLEASE CHANGE UPDATED DATE IN QUERY **/
INSERT INTO `m2_uat_migrated`.`wishlist_item`
SELECT owi.* FROM `wam1_nonprod_delta`.`wishlist_item` AS owi
INNER JOIN `m2_uat_migrated`.`wishlist` AS ww ON owi.`wishlist_id` = ww.`wishlist_id`
INNER JOIN `m2_uat_migrated`.`sequence_product` AS wsp ON owi.`product_id` = wsp.`sequence_value`
WHERE DATE(ww.updated_at) > '2020-09-04 23:14:50'
ON DUPLICATE KEY UPDATE `wishlist_id` = owi.`wishlist_id`,product_id = owi.product_id, store_id = owi.store_id, description = owi.description, qty = owi.qty;
/** End - Move updated and new rows from wishlist_item table in M1 database to M2 database **/

/** Start - Move updated and new rows from wishlist_item_option table in M1 database to M2 database **/
/** PLEASE CHANGE UPDATED DATE IN QUERY **/
INSERT INTO `m2_uat_migrated`.`wishlist_item_option`
SELECT owip.* FROM `wam1_nonprod_delta`.`wishlist_item_option` AS owip
INNER JOIN m2_uat_migrated.`wishlist_item` AS wwi ON owip.`wishlist_item_id` = wwi.`wishlist_item_id`
INNER JOIN `m2_uat_migrated`.`wishlist` AS ww ON ww.`wishlist_id` = wwi.`wishlist_id`
INNER JOIN `m2_uat_migrated`.`sequence_product` AS wsp ON owip.`product_id` = wsp.`sequence_value`
WHERE DATE(ww.updated_at) > '2020-09-04 23:14:50' AND owip.`code` = 'info_buyRequest'
ON DUPLICATE KEY UPDATE `wishlist_item_id` = owip.`wishlist_item_id`, product_id = owip.product_id, `code`=owip.`code`, `value`=owip.`value`;
/** End - Move updated and new rows from wishlist_item_option table in M1 database to M2 database **/

/** Start - Delete rows from wishlist_item_option table in M2 those are not available in M1 wishlist_item_option table **/
DELETE FROM `m2_uat_migrated`.`wishlist_item_option`
WHERE `option_id`
NOT IN (SELECT owip.`option_id` FROM `wam1_nonprod_delta`.`wishlist_item_option` AS owip);
/** End - Delete rows from wishlist_item_option table in M2 those are not available in M1 wishlist_item_option table **/

/** Start - Delete rows from wishlist_item table in M2 those are not available in M1 wishlist_item table **/
DELETE FROM `m2_uat_migrated`.`wishlist_item` AS wwi
WHERE wwi.`wishlist_item_id`
NOT IN (SELECT `wishlist_item_id` FROM `wam1_nonprod_delta`.`wishlist_item`);
/** End - Delete rows from wishlist_item table in M2 those are not available in M1 wishlist_item table **/

/** Start - Delete rows from wishlist table in M2 those are not available in M1 wishlist table **/
DELETE FROM `m2_uat_migrated`.`wishlist`
WHERE `wishlist_id`
NOT IN (SELECT `wishlist_id` FROM `wam1_nonprod_delta`.`wishlist`);
/** End - Delete rows from wishlist table in M2 those are not available in M1 wishlist table **/




