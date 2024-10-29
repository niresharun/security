/****************************************************************************
Start: Destination M2 fields and tables creation
****************************************************************************/
/*ALTER TABLE sales_order
ADD `is_customized` TINYINT(1) DEFAULT '0' COMMENT 'Is Customized',
ADD `order_source_event` VARCHAR(64) DEFAULT '',
ADD `order_source_rep` VARCHAR(64) DEFAULT '',
ADD `uuid` VARCHAR(64) DEFAULT '',
ADD `quickBooksOrderNumber` VARCHAR(64) DEFAULT '',
ADD `customerOrderNumber` VARCHAR(64) DEFAULT '',
ADD `by_new_customer` SMALLINT(6) DEFAULT NULL,
ADD `source_name` VARCHAR(64) DEFAULT '',
ADD `source_id` VARCHAR(64) DEFAULT '',
ADD `custom_discount_percentage` VARCHAR(255) DEFAULT NULL COMMENT 'Custom Discount Percentage',
ADD `custom_discount_amount` VARCHAR(255) DEFAULT NULL COMMENT 'Custom Discount Amount',
ADD `print_status` VARCHAR(120) DEFAULT 'non-printed';*/


CREATE TABLE `grandriver_cc_customers` (
  `customer_id` INT(10) NOT NULL,
  `designer` INT(10) DEFAULT NULL,
  `invitation_accepted` TINYINT(1) DEFAULT '0',
  `active` TINYINT(1) DEFAULT '0',
  `price_multiplier` DECIMAL(12,4) DEFAULT NULL,
  `custom_price_multiplier` DECIMAL(12,2) DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


CREATE TABLE `grandriver_cc_designers` (
  `customer_id` INT(10) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `company_name` VARCHAR(255) DEFAULT NULL,
  `footer_contact_name` VARBINARY(255) DEFAULT NULL,
  `telephone` VARCHAR(255) DEFAULT NULL,
  `telephone_mobile` VARCHAR(255) DEFAULT NULL,
  `fax` VARCHAR(255) DEFAULT NULL,
  `address_line_1` VARCHAR(255) DEFAULT NULL,
  `address_line_2` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(255) DEFAULT NULL,
  `state` VARCHAR(255) DEFAULT NULL,
  `postal_code` VARCHAR(255) DEFAULT NULL,
  `welcome_message` TEXT,
  `default_price_multiplier` FLOAT DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `grandriver_cc_wishlist_sharing` (
  `entity_id` INT(10) NOT NULL AUTO_INCREMENT,
  `wishlist_id` INT(10) UNSIGNED DEFAULT NULL,
  `cc_shared` TINYINT(1) DEFAULT '0',
  `cc_shared_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cc_shared_with` INT(10) UNSIGNED DEFAULT NULL,
  `cc_viewed_on` TIMESTAMP NULL DEFAULT NULL,
  `cc_show_price` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'show price or not',
  PRIMARY KEY (`entity_id`),
  KEY `FK_wishlist_id_idx` (`wishlist_id`),
  KEY `FK_customer_id_idx` (`cc_shared_with`),
  CONSTRAINT `FK_customer_id` FOREIGN KEY (`cc_shared_with`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_wishlist_id` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlist` (`wishlist_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=INNODB AUTO_INCREMENT=658 DEFAULT CHARSET=utf8;


CREATE TABLE `grandriver_extended_shipping` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `store` VARCHAR(255) NOT NULL DEFAULT '',
  `shipping_info` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=58971 DEFAULT CHARSET=utf8;


CREATE TABLE `webforms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `code` VARCHAR(255) NOT NULL,
  `redirect_url` TEXT NOT NULL,
  `description` TEXT NOT NULL,
  `success_text` TEXT NOT NULL,
  `registered_only` TINYINT(1) NOT NULL,
  `send_email` TINYINT(1) NOT NULL,
  `add_header` TINYINT(1) NOT NULL DEFAULT '1',
  `duplicate_email` TINYINT(1) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_template_id` INT(11) NOT NULL,
  `email_customer_template_id` INT(11) NOT NULL,
  `survey` TINYINT(1) NOT NULL,
  `approve` TINYINT(1) NOT NULL,
  `captcha_mode` VARCHAR(40) NOT NULL,
  `files_upload_limit` INT(11) NOT NULL DEFAULT '0',
  `images_upload_limit` INT(11) NOT NULL DEFAULT '0',
  `created_time` DATETIME DEFAULT NULL,
  `update_time` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  `menu` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE `webforms_fields` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `webform_id` INT(11) NOT NULL,
  `fieldset_id` INT(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `comment` TEXT NOT NULL,
  `result_label` VARCHAR(255) NOT NULL,
  `result_display` VARCHAR(10) NOT NULL DEFAULT 'on',
  `code` VARCHAR(255) NOT NULL,
  `type` VARCHAR(100) NOT NULL,
  `size` VARCHAR(20) NOT NULL,
  `value` TEXT NOT NULL,
  `email_subject` TINYINT(1) NOT NULL,
  `css_class` VARCHAR(255) NOT NULL,
  `css_style` VARCHAR(255) NOT NULL,
  `validate_message` TEXT NOT NULL,
  `validate_regex` VARCHAR(255) NOT NULL,
  `validate_length_max` INT(11) NOT NULL DEFAULT '0',
  `validate_length_min` INT(11) NOT NULL DEFAULT '0',
  `position` INT(11) NOT NULL,
  `required` TINYINT(1) NOT NULL,
  `created_time` DATETIME NOT NULL,
  `update_time` DATETIME NOT NULL,
  `is_active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;


CREATE TABLE `webforms_fieldsets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `webform_id` INT(11) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `result_display` VARCHAR(10) NOT NULL DEFAULT 'on',
  `position` INT(11) NOT NULL,
  `created_time` DATETIME NOT NULL,
  `update_time` DATETIME NOT NULL,
  `is_active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


CREATE TABLE `webforms_results` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `webform_id` INT(11) NOT NULL,
  `store_id` INT(11) NOT NULL,
  `customer_id` INT(11) NOT NULL,
  `customer_ip` BIGINT(20) NOT NULL,
  `created_time` DATETIME NOT NULL,
  `update_time` DATETIME NOT NULL,
  `approved` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=3647 DEFAULT CHARSET=utf8;


CREATE TABLE `webforms_results_values` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `result_id` INT(11) NOT NULL,
  `field_id` INT(11) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `result_id` (`result_id`,`field_id`)
) ENGINE=INNODB AUTO_INCREMENT=22097 DEFAULT CHARSET=utf8;


CREATE TABLE `wendover_customer_catalog_template` (
  `template_id` INT(11) NOT NULL AUTO_INCREMENT,
  `template_name` VARCHAR(500) DEFAULT NULL,
  `template_file` VARCHAR(500) DEFAULT NULL,
  `template_drop_spots_count` INT(11) DEFAULT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=INNODB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


CREATE TABLE `wendover_customer_gallery_catalog` (
  `catalog_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) DEFAULT NULL,
  `wishlist_id` INT(11) DEFAULT NULL,
  `logo_image` VARCHAR(500) DEFAULT NULL,
  `catalog_title` TEXT,
  `additional_info_1` TEXT,
  `additional_info_2` TEXT,
  `name` TEXT,
  `phone_number` VARCHAR(500) DEFAULT NULL,
  `website_url` VARCHAR(500) DEFAULT NULL,
  `company_name` VARCHAR(500) DEFAULT NULL,
  `created_date` DATETIME DEFAULT NULL,
  `updated_date` DATETIME DEFAULT NULL,
  `price_on` INT(11) DEFAULT NULL,
  `price_modifier` FLOAT DEFAULT NULL,
  `catalog_uuid` VARCHAR(64) DEFAULT '',
  PRIMARY KEY (`catalog_id`)
) ENGINE=INNODB AUTO_INCREMENT=19574 DEFAULT CHARSET=utf8;


CREATE TABLE `wendover_customer_gallery_catalog_page` (
  `page_id` INT(11) NOT NULL AUTO_INCREMENT,
  `catalog_id` INT(11) DEFAULT NULL,
  `page_template_id` INT(11) DEFAULT NULL,
  `drop_spot_config` TEXT,
  `page_position` INT(11) DEFAULT NULL,
  `created_date` DATETIME DEFAULT NULL,
  `updated_date` DATETIME DEFAULT NULL,
  `page_uuid` VARCHAR(64) DEFAULT '',
  PRIMARY KEY (`page_id`),
  KEY `FK1` (`catalog_id`),
  KEY `FK2` (`page_template_id`),
  CONSTRAINT `FK1` FOREIGN KEY (`catalog_id`) REFERENCES `wendover_customer_gallery_catalog` (`catalog_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK2` FOREIGN KEY (`page_template_id`) REFERENCES `wendover_customer_catalog_template` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB AUTO_INCREMENT=190794 DEFAULT CHARSET=utf8;


CREATE TABLE `board_design` (
  `design_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `time_created` INT(11) NOT NULL,
  `time_lastsave` INT(11) NOT NULL,
  `data` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `shared` INT(11) NOT NULL DEFAULT '0',
  `price` INT(11) NOT NULL,
  `item_count` INT(11) NOT NULL,
  `is_public` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `store_id` INT(11) NOT NULL,
  PRIMARY KEY (`design_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


CREATE TABLE `custom_product_cart` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `frame` VARCHAR(255) NOT NULL,
  `bottom_mat` VARCHAR(255) NOT NULL,
  `top_mat` VARCHAR(255) NOT NULL,
  `price` VARCHAR(255) NOT NULL,
  `quantity` INT(11) UNSIGNED NOT NULL,
  `quote_id` VARCHAR(255) NOT NULL,
  `item_id` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `custom_product_catalog` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `frame` VARCHAR(255) NOT NULL,
  `bottom_mat` VARCHAR(255) NOT NULL,
  `top_mat` VARCHAR(255) NOT NULL,
  `price` VARCHAR(255) NOT NULL,
  `quantity` INT(11) UNSIGNED NOT NULL,
  `catalog_id` VARCHAR(255) NOT NULL,
  `item_id` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


CREATE TABLE `custom_product_order` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `frame` VARCHAR(255) NOT NULL,
  `bottom_mat` VARCHAR(255) NOT NULL,
  `top_mat` VARCHAR(255) NOT NULL,
  `price` VARCHAR(255) NOT NULL,
  `quantity` INT(11) UNSIGNED NOT NULL,
  `quote_id` VARCHAR(255) NOT NULL,
  `item_id` VARCHAR(255) NOT NULL,
  `order_id` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


CREATE TABLE `custom_product_wishlist` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `frame` VARCHAR(255) NOT NULL,
  `bottom_mat` VARCHAR(255) NOT NULL,
  `top_mat` VARCHAR(255) NOT NULL,
  `price` VARCHAR(255) NOT NULL,
  `quantity` INT(11) UNSIGNED NOT NULL,
  `wishlist_id` VARCHAR(255) NOT NULL,
  `item_id` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `perficient_mycatalog_deletions` (
  `deletion_event_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Catalog Delete Event Primary ID',
  `catalog_id` INT(10) UNSIGNED NOT NULL COMMENT 'Catalog ID',
  `wishlist_id` INT(10) UNSIGNED NOT NULL COMMENT 'Wishlist ID',
  `updated_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Last updated date',
  `action` VARCHAR(100) DEFAULT NULL COMMENT 'Action',
  PRIMARY KEY (`deletion_event_id`),
  KEY `IDX_WISHLIST_ID` (`wishlist_id`)
) ENGINE=INNODB AUTO_INCREMENT=1119 DEFAULT CHARSET=utf8 COMMENT='Catalog Delete Logger Table';

CREATE TABLE `perficient_wishlist_deletions` (
  `deletion_event_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Wishlist Delete Event Primary ID',
  `wishlist_id` INT(10) UNSIGNED NOT NULL COMMENT 'Wishlist ID',
  `updated_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Last updated date',
  `action` VARCHAR(100) DEFAULT NULL COMMENT 'Action',
  PRIMARY KEY (`deletion_event_id`),
  KEY `IDX_WISHLIST_ID` (`wishlist_id`)
) ENGINE=INNODB AUTO_INCREMENT=4063 DEFAULT CHARSET=utf8 COMMENT='Wishlist Delete Logger Table';


CREATE TABLE `permission_block` (
  `block_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Block ID',
  `block_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Block Name',
  `is_allowed` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Mark that block can be processed by filters',
  PRIMARY KEY (`block_id`),
  UNIQUE KEY `UNQ_PERMISSION_BLOCK_BLOCK_NAME` (`block_name`)
) ENGINE=INNODB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='System blocks that can be processed via content filter';


CREATE TABLE `permission_variable` (
  `variable_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Variable ID',
  `variable_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Config Path',
  `is_allowed` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Mark that config can be processed by filters',
  PRIMARY KEY (`variable_id`,`variable_name`),
  UNIQUE KEY `UNQ_PERMISSION_VARIABLE_VARIABLE_NAME` (`variable_name`)
) ENGINE=INNODB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='System variables that can be processed via content filter';


CREATE TABLE `xtcore_config_data` (
  `config_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(255) NOT NULL DEFAULT 'general',
  `value` TEXT NOT NULL,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_scope` (`path`)
) ENGINE=INNODB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE `xtento_orderexport_destination` (
  `destination_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `hostname` VARCHAR(255) NOT NULL,
  `port` INT(5) DEFAULT NULL,
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `timeout` INT(5) NOT NULL DEFAULT '15',
  `path` VARCHAR(255) NOT NULL,
  `ftp_type` ENUM('','ftp','ftps') NOT NULL,
  `ftp_pasv` INT(1) NOT NULL DEFAULT '1',
  `email_sender` VARCHAR(255) NOT NULL COMMENT 'E-Mail Destination',
  `email_recipient` VARCHAR(255) NOT NULL COMMENT 'E-Mail Destination',
  `email_subject` VARCHAR(255) NOT NULL COMMENT 'E-Mail Destination',
  `email_body` TEXT NOT NULL COMMENT 'E-Mail Destination',
  `email_attach_files` INT(1) NOT NULL DEFAULT '1',
  `custom_class` VARCHAR(255) NOT NULL,
  `custom_function` VARCHAR(255) NOT NULL,
  `do_retry` INT(1) NOT NULL DEFAULT '1',
  `last_result` INT(1) NOT NULL,
  `last_result_message` TEXT NOT NULL,
  `last_modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`destination_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

/***********************************************************************************/
ALTER TABLE `xtento_orderexport_destination` MODIFY ftp_type VARCHAR(50) NOT NULL;
/***********************************************************************************/
CREATE TABLE `xtento_orderexport_log` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_id` INT(9) NOT NULL,
  `files` TEXT NOT NULL,
  `destination_ids` TEXT NOT NULL,
  `export_type` INT(9) NOT NULL,
  `export_event` VARCHAR(255) NOT NULL,
  `records_exported` INT(9) NOT NULL,
  `result` INT(1) NOT NULL,
  `result_message` TEXT NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_index` (`profile_id`,`created_at`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

/***********************************************************************************/
-- ALTER TABLE xtento_orderexport_log MODIFY log_id INT UNSIGNED NOT NULL;
/***********************************************************************************/
CREATE TABLE `xtento_orderexport_profile` (
  `profile_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `entity` VARCHAR(255) NOT NULL,
  `enabled` INT(1) NOT NULL,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `destination_ids` VARCHAR(255) NOT NULL,
  `last_execution` DATETIME DEFAULT NULL,
  `last_modification` DATETIME DEFAULT NULL,
  `conditions_serialized` TEXT NOT NULL,
  `store_ids` TEXT NOT NULL,
  `export_fields` TEXT NOT NULL,
  `customer_groups` VARCHAR(255) NOT NULL DEFAULT '',
  `export_one_file_per_object` INT(1) NOT NULL DEFAULT '0',
  `export_filter_product_type` VARCHAR(255) NOT NULL,
  `export_filter_new_only` INT(1) NOT NULL,
  `export_filter_datefrom` DATE DEFAULT NULL,
  `export_filter_older_x_minutes` INT(10) DEFAULT NULL,
  `export_filter_last_x_days` INT(10) DEFAULT NULL,
  `export_filter_dateto` DATE DEFAULT NULL,
  `export_filter_status` MEDIUMTEXT NOT NULL,
  `export_action_change_status` VARCHAR(255) NOT NULL,
  `export_action_add_comment` TEXT,
  `export_action_cancel_order` INT(1) NOT NULL DEFAULT '0',
  `export_action_invoice_order` INT(1) NOT NULL DEFAULT '0',
  `export_action_invoice_notify` INT(1) NOT NULL DEFAULT '0',
  `export_action_ship_order` INT(1) NOT NULL DEFAULT '0',
  `export_action_ship_notify` INT(1) NOT NULL DEFAULT '0',
  `save_files_manual_export` INT(1) NOT NULL DEFAULT '1',
  `export_empty_files` INT(1) NOT NULL DEFAULT '0',
  `manual_export_enabled` INT(1) NOT NULL DEFAULT '1',
  `start_download_manual_export` INT(1) NOT NULL DEFAULT '1',
  `save_files_local_copy` INT(1) NOT NULL DEFAULT '1',
  `event_observers` VARCHAR(255) NOT NULL,
  `cronjob_enabled` INT(1) NOT NULL DEFAULT '0',
  `cronjob_frequency` VARCHAR(255) NOT NULL,
  `cronjob_custom_frequency` VARCHAR(255) NOT NULL,
  `output_type` VARCHAR(255) NOT NULL DEFAULT 'xsl',
  `filename` VARCHAR(255) NOT NULL,
  `encoding` VARCHAR(255) NOT NULL,
  `xsl_template` MEDIUMTEXT NOT NULL,
  `test_id` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`profile_id`)
) ENGINE=INNODB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


ALTER TABLE xtento_orderexport_profile MODIFY profile_id INT UNSIGNED NOT NULL;


CREATE TABLE `xtento_orderexport_profile_history` (
  `history_id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_id` INT(11) NOT NULL,
  `log_id` INT(11) NOT NULL,
  `entity` VARCHAR(255) NOT NULL COMMENT 'Export Entity',
  `entity_id` INT(11) NOT NULL,
  `exported_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `ENTITY_ID` (`entity`,`entity_id`),
  KEY `PROFILE_ID` (`profile_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='Export history of objects exported for profile';

/****************************************************************************
End: Destination M2 fields and tables creation
****************************************************************************/



