/**
 * ##################################################
 * ########### Delta Migration Start Here ###########
 * ##################################################
 */

/************************************************************************
-- START: DELTA MIGRATION - CUSTOMER
*************************************************************************/
/**
We will require to update database name for following queries of customer delta migration when it will be executed on UAT and PROD server
M1 Database name: wam1_nonprod_delta
M2 Database Name: wendover_m2_live
*/


/**   UPDATE emails of M1 database customer_entity to prefix by 'perficienttest-'  ***/


/** Temp table for customer **/
CREATE TABLE IF NOT EXISTS `perficient_customer_temp` (
  `entity_id` BIGINT(20) NOT NULL COMMENT 'Customer ID',
  `email` VARCHAR(255) DEFAULT NULL COMMENT 'Email',
  `group_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Group ID',
  `is_active` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Is Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created At'
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='Perficient Customer Temp Table';

/** Create Table In M2 database:  perficient_customer_attribute_temp  **/

CREATE TABLE IF NOT EXISTS `perficient_customer_attribute_temp` (
  `entity_id` BIGINT(20) NOT NULL COMMENT 'Customer ID',
  `attribute_code` VARCHAR(255) DEFAULT NULL COMMENT 'Attribute Code',
  `value` TEXT DEFAULT NULL COMMENT 'Attribute Value',
  `option_id` INT(11) DEFAULT NULL COMMENT 'Attribute option ID'
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='Perficient Customer Attribute Temp Table';

INSERT INTO `eav_entity_attribute` (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order)
SELECT 1, 1, 1, attribute_id, 810 AS sort_order FROM eav_attribute WHERE attribute_code IN ("no_of_stores", "reg_comments")
AND entity_type_id = 1
ON DUPLICATE KEY UPDATE `sort_order` = VALUES(`sort_order`);

/** Insert data into M2 perficient_customer_temp from M1 customer_entity table **/
INSERT INTO wendover_m2_live.perficient_customer_temp (entity_id, email, group_id, is_active, created_at)
SELECT ce.entity_id, ce.email, ce.group_id, ce.is_active, ce.created_at FROM wam1_nonprod_delta.customer_entity AS ce
WHERE ce.updated_at > '2021-09-02 21:48:57'
ORDER BY updated_at;

/** Insert data into M2 perficient_customer_attribute_temp from M1 customer_entity_varchar table excluding taxvat attribute **/
INSERT INTO wendover_m2_live.perficient_customer_attribute_temp (entity_id, attribute_code, VALUE)
SELECT cev.entity_id, ea.attribute_code, cev.value FROM wam1_nonprod_delta.customer_entity_varchar AS cev
INNER JOIN wam1_nonprod_delta.eav_attribute AS ea ON ea.attribute_id = cev.attribute_id AND ea.entity_type_id = 1 AND ea.frontend_input NOT IN ('select', 'multiselect') AND ea.attribute_code NOT IN ( 'taxvat', 'no_of_jobs_per_year', 'type_of_projects')
INNER JOIN wendover_m2_live.perficient_customer_temp AS pct ON pct.entity_id = cev.entity_id;

/** Insert data into M2 perficient_customer_attribute_temp from M1 customer_entity_varchar table for select attributes  **/
INSERT INTO wendover_m2_live.perficient_customer_attribute_temp (entity_id, attribute_code, VALUE)
SELECT cev.entity_id, ea.attribute_code, eaov.value FROM wam1_nonprod_delta.customer_entity_varchar AS cev
INNER JOIN wam1_nonprod_delta.eav_attribute AS ea ON ea.attribute_id = cev.attribute_id AND ea.entity_type_id = 1 AND
ea.frontend_input = 'select'
INNER JOIN wendover_m2_live.perficient_customer_temp AS pct ON pct.entity_id = cev.entity_id
INNER JOIN wam1_nonprod_delta.eav_attribute_option_value AS eaov ON eaov.option_id = cev.value;

/** Insert data into M2 perficient_customer_attribute_temp from M1 customer_entity_varchar table for multiselect attributes  **/
INSERT INTO wendover_m2_live.perficient_customer_attribute_temp (entity_id, attribute_code, VALUE)
SELECT  cei.entity_id, ea.attribute_code,
        GROUP_CONCAT(eaov.value ORDER BY eaov.option_id) optionLabel
FROM wam1_nonprod_delta.customer_entity_varchar AS cei
INNER JOIN wam1_nonprod_delta.eav_attribute AS ea ON ea.attribute_id = cei.attribute_id AND ea.entity_type_id = 1 AND ea.attribute_code = 'des_comm'
INNER JOIN wendover_m2_live.perficient_customer_temp AS pct ON pct.entity_id = cei.entity_id
INNER JOIN wam1_nonprod_delta.eav_attribute_option_value AS eaov
            ON FIND_IN_SET(eaov.option_id, cei.value) > 0
GROUP BY cei.entity_id;


/** Insert data into M2 perficient_customer_attribute_temp from M1 customer_entity_text table  **/
INSERT INTO wendover_m2_live.perficient_customer_attribute_temp (entity_id, attribute_code, VALUE)
SELECT cet.entity_id, ea.attribute_code, cet.value FROM wam1_nonprod_delta.customer_entity_text AS cet
INNER JOIN wam1_nonprod_delta.eav_attribute AS ea ON ea.attribute_id = cet.attribute_id AND ea.entity_type_id = 1
INNER JOIN wendover_m2_live.perficient_customer_temp AS pct ON pct.entity_id = cet.entity_id;

/** Insert data into M2 perficient_customer_attribute_temp from M1 customer_entity_int table  **/
INSERT INTO wendover_m2_live.perficient_customer_attribute_temp (entity_id, attribute_code, VALUE)
SELECT cei.entity_id, ea.attribute_code, cei.value FROM wam1_nonprod_delta.customer_entity_int AS cei
INNER JOIN wam1_nonprod_delta.eav_attribute AS ea ON ea.attribute_id = cei.attribute_id AND ea.entity_type_id = 1 AND ea.attribute_code NOT IN( 'default_billing', 'default_shipping', 'password_created_at', 'reward_update_notification', 'reward_warning_notification', 'sales_rep', 'gender', 'customer_activated')
INNER JOIN wendover_m2_live.perficient_customer_temp AS pct ON pct.entity_id = cei.entity_id;

/** Insert data into M2 perficient_customer_attribute_temp from M1 customer_entity_int table for select attributes  **/
INSERT INTO wendover_m2_live.perficient_customer_attribute_temp (entity_id, attribute_code, VALUE)
SELECT cei.entity_id, ea.attribute_code, eaov.value FROM wam1_nonprod_delta.customer_entity_int AS cei
INNER JOIN wam1_nonprod_delta.eav_attribute AS ea ON ea.attribute_id = cei.attribute_id AND ea.entity_type_id = 1 AND ea.attribute_code IN( 'sales_rep', 'gender')
INNER JOIN wendover_m2_live.perficient_customer_temp AS pct ON pct.entity_id = cei.entity_id
INNER JOIN wam1_nonprod_delta.eav_attribute_option_value AS eaov ON eaov.option_id = cei.value;

/** Insert data into M2 perficient_customer_attribute_temp from M1 customer_entity_int table for boolean attributes  **/
INSERT INTO wendover_m2_live.perficient_customer_attribute_temp (entity_id, attribute_code, VALUE)
SELECT cei.entity_id, ea.attribute_code, if (cei.value = 0, 'No', 'Yes') as value FROM wam1_nonprod_delta.customer_entity_int AS cei
INNER JOIN wam1_nonprod_delta.eav_attribute AS ea ON ea.attribute_id = cei.attribute_id AND ea.entity_type_id = 1 AND ea.attribute_code = 'customer_activated'
INNER JOIN wendover_m2_live.perficient_customer_temp AS pct ON pct.entity_id = cei.entity_id;


/* Start: update price multiplier value for all customers in M2 */
UPDATE wendover_m2_live.perficient_customer_attribute_temp AS pcat
SET pcat.`value` = CASE
		WHEN pcat.`value` = '1x' THEN "1.00X"
		WHEN pcat.`value` = '1.25x' THEN "1.25X"
		WHEN pcat.`value` = '1.5x' THEN "1.50X"
		WHEN pcat.`value` = '1.75x' THEN "1.75X"
		WHEN pcat.`value` = '2x' THEN "2.00X"
		WHEN pcat.`value` = '2.25x' THEN "2.25X"
		WHEN pcat.`value` = '2.5x' THEN "2.50X"
		WHEN pcat.`value` = '2.75x' THEN "2.75X"
		WHEN pcat.`value` = '3x' THEN "3.00X"
		WHEN pcat.`value` = '3.25x' THEN "3.25X"
		WHEN pcat.`value` = '3.5x' THEN "3.50X"
		WHEN pcat.`value` = '3.75x' THEN "3.75X"
		WHEN pcat.`value` = '4x' THEN "4.00X"
	END
WHERE pcat.attribute_code = "price_multiplier"
  AND pcat.`value` IS NOT NULL;

/** Insert data for attribute taxvat ***/
INSERT INTO wendover_m2_live.perficient_customer_attribute_temp (entity_id, attribute_code, VALUE)
SELECT cev.entity_id, 'taxvat', cev.value FROM wam1_nonprod_delta.customer_entity_varchar AS cev
INNER JOIN wam1_nonprod_delta.eav_attribute AS ea ON ea.attribute_id = cev.attribute_id AND ea.entity_type_id = 1 AND ea.attribute_code = 'tax_id'
INNER JOIN wendover_m2_live.perficient_customer_temp AS pct ON pct.entity_id = cev.entity_id;


/************************************************************************
-- END: DELTA MIGRATION - CUSTOMER
*************************************************************************/



/************************************************************************
-- START: DELTA MIGRATION - ADDRESS
*************************************************************************/
/** Create Table In M2 database:  perficient_customer_address_temp  **/
CREATE TABLE IF NOT EXISTS `perficient_customer_address_temp` (
  `entity_id` INT(10) UNSIGNED NOT NULL COMMENT 'Entity ID',
  `increment_id` VARCHAR(50) DEFAULT NULL COMMENT 'Increment ID',
  `parent_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'Parent ID',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created At',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated At',
  `is_active` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Is Active',
  `city` VARCHAR(255) NOT NULL COMMENT 'City',
  `company` VARCHAR(255) DEFAULT NULL COMMENT 'Company',
  `country_id` VARCHAR(255) NULL COMMENT 'Country',
  `fax` VARCHAR(255) DEFAULT NULL COMMENT 'Fax',
  `firstname` VARCHAR(255) NOT NULL COMMENT 'First Name',
  `lastname` VARCHAR(255) NOT NULL COMMENT 'Last Name',
  `middlename` VARCHAR(255) DEFAULT NULL COMMENT 'Middle Name',
  `postcode` VARCHAR(255) DEFAULT NULL COMMENT 'Zip/Postal Code',
  `prefix` VARCHAR(40) DEFAULT NULL COMMENT 'Name Prefix',
  `region` VARCHAR(255) DEFAULT NULL COMMENT 'State/Province',
  `region_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'State/Province',
  `street` TEXT NOT NULL COMMENT 'Street Address',
  `suffix` VARCHAR(40) DEFAULT NULL COMMENT 'Name Suffix',
  `telephone` VARCHAR(255) NOT NULL COMMENT 'Phone Number',
  `vat_id` VARCHAR(255) DEFAULT NULL COMMENT 'VAT number',
  `vat_is_valid` INT(10) UNSIGNED DEFAULT NULL COMMENT 'VAT number validity',
  `vat_request_date` VARCHAR(255) DEFAULT NULL COMMENT 'VAT number validation request date',
  `vat_request_id` VARCHAR(255) DEFAULT NULL COMMENT 'VAT number validation request ID',
  `vat_request_success` INT(10) UNSIGNED DEFAULT NULL COMMENT 'VAT number validation request success'
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='Perficient Customer Address Entity Temp Table';


/** Insert customer address data into temp table from M1 **/
INSERT INTO wendover_m2_live.perficient_customer_address_temp
(entity_id, increment_id, parent_id, created_at, updated_at, is_active, city, company, country_id, fax, firstname, lastname, middlename, postcode, prefix, region, region_id, street, suffix, telephone, vat_id, vat_is_valid, vat_request_date, vat_request_id, vat_request_success)
SELECT custAdd.entity_id, custAdd.increment_id, custAdd.parent_id, custAdd.created_at, custAdd.updated_at, custAdd.is_active, addCity.value AS `city`, addCom.value AS `company`, addContId.value AS `country_id`, addFax.value AS `fax`, addFname.value AS `firstname`, addLname.value AS `lastname`, addMname.value AS `middlename`, addPost.value AS `postcode`, addPrefix.value AS `prefix`, addRegion.value AS `region`, addRegionId.value AS `region_id`, addStreet.value AS `street`, addSuffix.value AS `suffix`, addPhone.value AS `telephone`, addVatId.value AS `vat_id`, addVatIsValid.value AS `vat_is_valid`, addVatReqDate.value AS `vat_request_date`, addVatReqId.value AS `vat_request_id`, addVatReqSuc.value AS `vat_request_success`
FROM `wam1_nonprod_delta`.`customer_address_entity` AS custAdd
LEFT JOIN `customer_address_entity_varchar` AS addCity ON addCity.entity_id = custAdd.entity_id AND addCity.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'city' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addCom ON addCom.entity_id = custAdd.entity_id AND addCom.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'company' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addContId ON addContId.entity_id = custAdd.entity_id AND addContId.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'country_id' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addFax ON addFax.entity_id = custAdd.entity_id AND addFax.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'fax' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addFname ON addFname.entity_id = custAdd.entity_id AND addFname.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'firstname' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addLname ON addLname.entity_id = custAdd.entity_id AND addLname.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'lastname' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addMname ON addMname.entity_id = custAdd.entity_id AND addMname.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'middlename' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addPost ON addPost.entity_id = custAdd.entity_id AND addPost.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'postcode' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addPrefix ON addPrefix.entity_id = custAdd.entity_id AND addPrefix.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'prefix' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addRegion ON addRegion.entity_id = custAdd.entity_id AND addRegion.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'region' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_int` AS addRegionId ON addRegionId.entity_id = custAdd.entity_id AND addRegionId.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'region_id' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_text` AS addStreet ON addStreet.entity_id = custAdd.entity_id AND addStreet.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'street' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addSuffix ON addSuffix.entity_id = custAdd.entity_id AND addSuffix.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'suffix' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addPhone ON addPhone.entity_id = custAdd.entity_id AND addPhone.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'telephone' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addVatId ON addVatId.entity_id = custAdd.entity_id AND addVatId.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'vat_id' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_int` AS addVatIsValid ON addVatIsValid.entity_id = custAdd.entity_id AND addVatIsValid.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'vat_is_valid' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addVatReqDate ON addVatReqDate.entity_id = custAdd.entity_id AND addVatReqDate.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'vat_request_date' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_varchar` AS addVatReqId ON addVatReqId.entity_id = custAdd.entity_id AND addVatReqId.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'vat_request_id' AND entity_type_id = 2)
LEFT JOIN `customer_address_entity_int` AS addVatReqSuc ON addVatReqSuc.entity_id = custAdd.entity_id AND addVatReqSuc.attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE attribute_code = 'vat_request_success' AND entity_type_id = 2)
WHERE custAdd.updated_at > '2021-09-02 21:48:57';


/*** Query to get all the invalid address records from perficient_customer_address_temp table ***/
SELECT cae.entity_id AS address_entity_id, ce.email AS customer_email, cae.firstname, cae.lastname,
cae.street, cae.city, cae.region, cae.region_id, cae.postcode, cae.country_id, cae.telephone
FROM wendover_m2_live.perficient_customer_address_temp AS cae
INNER JOIN customer_entity AS ce ON ce.entity_id = cae.parent_id
WHERE cae.firstname IS NULL OR cae.firstname = "" OR cae.firstname = '*'
OR cae.lastname IS NULL OR cae.lastname = "" OR cae.lastname = '*'
OR cae.street IS NULL OR cae.street = ""
OR cae.city IS NULL OR cae.city = "" OR cae.city = "*"
OR cae.region IS NULL OR cae.region = "" OR cae.region = "*"
OR cae.region_id IS NULL OR cae.region_id = "" OR cae.region_id = "*" OR cae.region_id = 0
OR cae.country_id IS NULL OR cae.country_id = "" OR cae.country_id = "*"
OR cae.telephone IS NULL OR cae.telephone = "" OR cae.telephone = "*";


/*** Delete all the invalid address records from perficient_customer_address_temp table. ***/
DELETE cae.*
FROM wendover_m2_live.perficient_customer_address_temp AS cae
INNER JOIN wendover_m2_live.customer_entity AS ce ON ce.entity_id = cae.parent_id
WHERE cae.firstname IS NULL OR cae.firstname = '' OR cae.firstname = '*'
OR cae.lastname IS NULL OR cae.lastname = '' OR cae.lastname = '*'
OR cae.street IS NULL OR cae.street = ''
OR cae.city IS NULL OR cae.city = '' OR cae.city = '*'
OR cae.region IS NULL OR cae.region = '' OR cae.region = '*'
OR cae.region_id IS NULL OR cae.region_id = '' OR cae.region_id = 0
OR cae.country_id IS NULL OR cae.country_id = '' OR cae.country_id = '*'
OR cae.telephone IS NULL OR cae.telephone = '' OR cae.telephone = '*';


/*** Insert/Update address from perficient_customer_address_temp table to customer_address_entity ***/
INSERT INTO `wendover_m2_live`.`customer_address_entity` (entity_id, increment_id, parent_id, created_at, updated_at, is_active, city, company, country_id, fax, firstname, lastname, middlename, postcode, prefix, region, region_id, street, suffix, telephone, vat_id, vat_is_valid, vat_request_date, vat_request_id, vat_request_success)
SELECT tmp.entity_id, tmp.increment_id, tmp.parent_id, tmp.created_at, tmp.updated_at, tmp.is_active, tmp.city, tmp.company, tmp.country_id, tmp.fax, tmp.firstname, tmp.lastname, tmp.middlename, tmp.postcode, tmp.prefix, tmp.region, tmp.region_id, tmp.street, tmp.suffix, tmp.telephone, tmp.vat_id, tmp.vat_is_valid, tmp.vat_request_date, tmp.vat_request_id, tmp.vat_request_success
FROM `wendover_m2_live`.`perficient_customer_address_temp` AS tmp
INNER JOIN customer_entity AS ce ON ce.entity_id = tmp.parent_id
ON DUPLICATE KEY UPDATE entity_id = tmp.entity_id, increment_id = tmp.increment_id, parent_id = tmp.parent_id, created_at = tmp.created_at, updated_at = tmp.updated_at, is_active = tmp.is_active, city = tmp.city, company = tmp.company, country_id = tmp.country_id, fax = tmp.fax, firstname = tmp.firstname, lastname = tmp.lastname, middlename = tmp.middlename, postcode = tmp.postcode, prefix = tmp.prefix, region = tmp.region, region_id = tmp.region_id, street = tmp.street, suffix = tmp.suffix, telephone = tmp.telephone, vat_id = tmp.vat_id, vat_is_valid = tmp.vat_is_valid, vat_request_date = tmp.vat_request_date, vat_request_id = tmp.vat_request_id, vat_request_success = tmp.vat_request_success
;


/** Set billing-address **/
UPDATE wendover_m2_live.customer_entity AS ce, (SELECT cei.*
	FROM wam1_nonprod_delta.eav_attribute AS ea
	INNER JOIN wam1_nonprod_delta.customer_entity_int AS cei ON ea.attribute_id = cei.attribute_id
	INNER JOIN wendover_m2_live.customer_entity AS ce1 ON ce1.entity_id = cei.entity_id
	INNER JOIN wendover_m2_live.customer_address_entity AS cae ON cae.entity_id = ce1.default_billing
	WHERE ea.attribute_code = 'default_billing'
	AND ce1.updated_at > '2021-09-02 21:48:57'
	ORDER BY ce1.entity_id ASC) AS default_billing_data
SET ce.default_billing = default_billing_data.value
WHERE ce.entity_id = default_billing_data.entity_id
AND ce.updated_at > '2021-09-02 21:48:57';


/** Set shipping-address **/
UPDATE wendover_m2_live.customer_entity AS ce, (SELECT cei.*
	FROM wam1_nonprod_delta.eav_attribute AS ea
	INNER JOIN wam1_nonprod_delta.customer_entity_int AS cei ON ea.attribute_id = cei.attribute_id
	INNER JOIN wendover_m2_live.customer_entity AS ce1 ON ce1.entity_id = cei.entity_id
	INNER JOIN wendover_m2_live.customer_address_entity AS cae ON cae.entity_id = ce1.default_shipping
	WHERE ea.attribute_code = 'default_shipping'
	AND ce1.updated_at > '2021-09-02 21:48:57'
	ORDER BY ce1.entity_id ASC) AS default_shipping
SET ce.default_shipping = default_shipping.value
WHERE ce.entity_id = default_shipping.entity_id
AND ce.updated_at > '2021-09-02 21:48:57';

/************************************************************************
-- END: DELTA MIGRATION - ADDRESS
*************************************************************************/


/************************************************************************
-- START: DELTA MIGRATION - COMPANY
*************************************************************************/
/**
We will require to update database name for following queries of company-customer delta migration when it will be executed on UAT and PROD server
M1 Database name: wam1_nonprod_delta
M2 Database Name: wendover_m2_live
*/

/*************************/
-- Take backup of grandriver_cc_designers and grandriver_cc_customers tables
/*************************/

TRUNCATE TABLE grandriver_cc_designers;

TRUNCATE TABLE grandriver_cc_customers;

INSERT INTO `wendover_m2_live`.grandriver_cc_customers (customer_id, designer, invitation_accepted, active, price_multiplier, custom_price_multiplier)
SELECT gcc.customer_id, gcc.designer, gcc.invitation_accepted, gcc.active, gcc.price_multiplier, gcc.custom_price_multiplier
FROM `wam1_nonprod_delta`.grandriver_cc_customers AS gcc
INNER JOIN `wendover_m2_live`.perficient_customer_temp AS pct ON pct.entity_id = gcc.customer_id;

INSERT INTO `grandriver_cc_designers` (customer_id, email, telephone_count, import_status, company_name,
telephone, address_line_1, city, state, postal_code)
SELECT ce.entity_id AS customer_id, ce.email, NULL AS telephone_count, 0, TRIM(cev.value) AS company_name,
cae.telephone, cae.street AS address_line_1, cae.city, cae.region_id, cae.postcode AS postal_code
FROM customer_entity AS ce
LEFT JOIN `grandriver_cc_customers` AS gcc ON gcc.customer_id = ce.entity_id
INNER JOIN eav_attribute AS eav ON eav.attribute_code = "company" AND eav.entity_type_id = 1
LEFT JOIN customer_entity_varchar AS cev ON cev.entity_id = ce.entity_id AND cev.attribute_id = eav.attribute_id
LEFT JOIN customer_address_entity AS cae ON cae.entity_id = ce.default_billing
INNER JOIN perficient_customer_temp AS pct ON pct.entity_id = ce.entity_id
WHERE gcc.customer_id IS NULL;

UPDATE `grandriver_cc_designers`
SET cleaned_telephone = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telephone,'-',''), '.',''), '(',''), ')',''), '/',''), ' ',''), '+',''), '*',''), '{',''), '}','');

UPDATE `grandriver_cc_designers` SET cleaned_telephone = NULL WHERE cleaned_telephone = '' OR cleaned_telephone = '0' OR cleaned_telephone = '0000000000';

UPDATE `grandriver_cc_designers` AS `dest`,
(
    SELECT cleaned_telephone, COUNT(cleaned_telephone) AS cnt
    FROM `grandriver_cc_designers`
    GROUP BY cleaned_telephone
) AS `src`
SET `dest`.`telephone_count` = `src`.`cnt`
WHERE `dest`.cleaned_telephone = `src`.cleaned_telephone;

UPDATE `grandriver_cc_designers` SET cleaned_street = SUBSTRING(REPLACE(LOWER(TRIM(address_line_1)), " ", ""), 1, 10)
WHERE address_line_1 IS NOT NULL;

UPDATE `grandriver_cc_designers` SET cleaned_company_name = LOWER(TRIM(company_name))
WHERE company_name IS NOT NULL;

UPDATE `grandriver_cc_designers` SET cleaned_company_name = REPLACE(cleaned_company_name, '"', "'");

UPDATE `grandriver_cc_designers` AS `dest`,
(
    SELECT cleaned_company_name, COUNT(cleaned_company_name) AS cnt
    FROM `grandriver_cc_designers`
    GROUP BY cleaned_company_name
) AS `src`
SET `dest`.`company_name_count` = `src`.`cnt`
WHERE `dest`.cleaned_company_name = `src`.cleaned_company_name;

-- Start: Prepare delete query for invalid address
SELECT cae.entity_id FROM customer_address_entity AS cae
INNER JOIN customer_entity AS ce ON ce.entity_id = cae.parent_id
WHERE cae.firstname IS NULL OR cae.firstname = "" OR cae.firstname = "*"
OR cae.lastname IS NULL OR cae.lastname = "" OR cae.lastname = "*"
OR cae.street IS NULL OR cae.street = ""
OR cae.city IS NULL OR cae.city = "" OR cae.city = "*"
OR cae.region IS NULL OR cae.region = "" OR cae.region = "*"
OR cae.region_id IS NULL OR cae.region_id = "" OR cae.region_id = "*" OR cae.region_id = 0
OR cae.country_id IS NULL OR cae.country_id = "" OR cae.country_id = "*"
OR cae.telephone IS NULL OR cae.telephone = "" OR cae.telephone = "*";

/* *****************************************************  */
DELETE FROM customer_address_entity WHERE entity_id IN ();
/* *****************************************************  */


DELETE cae FROM customer_address_entity AS cae
LEFT JOIN `directory_country_region` AS dcr ON dcr.region_id = cae.region_id AND dcr.country_id = cae.country_id
WHERE dcr.region_id IS NULL AND dcr.country_id IS NULL;

UPDATE customer_entity AS ce
LEFT JOIN customer_address_entity AS cae ON cae.entity_id = ce.default_billing
SET ce.default_billing = NULL
WHERE cae.entity_id IS NULL AND ce.default_billing IS NOT NULL;

UPDATE customer_entity AS ce
LEFT JOIN customer_address_entity AS cae ON cae.entity_id = ce.default_shipping
SET ce.default_shipping = NULL
WHERE cae.entity_id IS NULL AND ce.default_shipping IS NOT NULL;

UPDATE `grandriver_cc_designers` SET `telephone` = NULL WHERE telephone = "" OR telephone = "*" OR telephone = "0";

UPDATE `grandriver_cc_designers` SET `address_line_1` = NULL WHERE address_line_1 = "" OR address_line_1 = "*" OR address_line_1 = "0";

UPDATE `grandriver_cc_designers` SET `address_line_2` = NULL WHERE address_line_2 = "" OR address_line_2 = "*" OR address_line_2 = "0";

UPDATE `grandriver_cc_designers` SET `city` = NULL WHERE city = "" OR city = "*" OR city = "0";

UPDATE `grandriver_cc_designers` SET `state` = NULL WHERE state = "" OR state = "*" OR state = "0";

UPDATE `grandriver_cc_designers` SET `postal_code` = NULL WHERE postal_code = "" OR postal_code = "*" OR postal_code = "0";

-- To fix issue of customer save during company creation
UPDATE eav_attribute SET is_required = 0
WHERE entity_type_id = 2
AND attribute_code = "location";
/************************************************************************
-- END: DELTA MIGRATION - COMPANY
*************************************************************************/




/************************************************************************
-- START: DELTA MIGRATION - Wishlist
*************************************************************************/
/** Start - Move updated and new rows from wishlist table in M1 database to M2 database **/
/** PLEASE CHANGE DATABASE NAMES AND UPDATED DATE IN QUERY **/
INSERT INTO `wendover_m2_live`.`wishlist`
SELECT ow.*,'' AS collaboration_ids FROM `wam1_nonprod_delta`.`wishlist` AS ow
INNER JOIN `wendover_m2_live`.`customer_entity` AS customer ON customer.entity_id = ow.customer_id
WHERE DATE(ow.updated_at) > '2021-09-02 22:18:03'
ON DUPLICATE KEY UPDATE `customer_id` = ow.`customer_id` , shared = ow.shared,sharing_code=ow.sharing_code,updated_at=ow.updated_at,`name`=ow.`name`,visibility=ow.visibility;
/** End - Move updated and new rows from wishlist table in M1 database to M2 database **/

/** Start - Move updated and new rows from wishlist_item table in M1 database to M2 database **/
/** PLEASE CHANGE UPDATED DATE IN QUERY **/
INSERT INTO `wendover_m2_live`.`wishlist_item`
SELECT owi.* FROM `wam1_nonprod_delta`.`wishlist_item` AS owi
INNER JOIN `wendover_m2_live`.`wishlist` AS ww ON owi.`wishlist_id` = ww.`wishlist_id`
INNER JOIN `wendover_m2_live`.`sequence_product` AS wsp ON owi.`product_id` = wsp.`sequence_value`
WHERE DATE(ww.updated_at) > '2021-09-02 22:18:03' AND owi.store_id != 2
ON DUPLICATE KEY UPDATE `wishlist_id` = owi.`wishlist_id`,product_id = owi.product_id, store_id = owi.store_id, description = owi.description, qty = owi.qty;
/** End - Move updated and new rows from wishlist_item table in M1 database to M2 database **/

/** Start - Move updated and new rows from wishlist_item_option table in M1 database to M2 database **/
/** PLEASE CHANGE UPDATED DATE IN QUERY **/
INSERT INTO `wendover_m2_live`.`wishlist_item_option`
SELECT owip.* FROM `wam1_nonprod_delta`.`wishlist_item_option` AS owip
INNER JOIN wendover_m2_live.`wishlist_item` AS wwi ON owip.`wishlist_item_id` = wwi.`wishlist_item_id`
INNER JOIN `wendover_m2_live`.`wishlist` AS ww ON ww.`wishlist_id` = wwi.`wishlist_id`
INNER JOIN `wendover_m2_live`.`sequence_product` AS wsp ON owip.`product_id` = wsp.`sequence_value`
WHERE DATE(ww.updated_at) > '2021-09-02 22:18:03' AND owip.`code` = 'info_buyRequest'
ON DUPLICATE KEY UPDATE `wishlist_item_id` = owip.`wishlist_item_id`, product_id = owip.product_id, `code`=owip.`code`, `value`=owip.`value`;
/** End - Move updated and new rows from wishlist_item_option table in M1 database to M2 database **/

/** Start - Delete rows from wishlist_item_option table in M2 those are not available in M1 wishlist_item_option table **/
DELETE FROM `wendover_m2_live`.`wishlist_item_option`
WHERE `option_id`
NOT IN (SELECT owip.`option_id` FROM `wam1_nonprod_delta`.`wishlist_item_option` AS owip);
/** End - Delete rows from wishlist_item_option table in M2 those are not available in M1 wishlist_item_option table **/

/** Start - Delete rows from wishlist_item table in M2 those are not available in M1 wishlist_item table **/
DELETE FROM `wendover_m2_live`.`wishlist_item` AS wwi
WHERE wwi.`wishlist_item_id`
NOT IN (SELECT `wishlist_item_id` FROM `wam1_nonprod_delta`.`wishlist_item`);
/** End - Delete rows from wishlist_item table in M2 those are not available in M1 wishlist_item table **/

/** Start - Delete rows from wishlist table in M2 those are not available in M1 wishlist table **/
DELETE FROM `wendover_m2_live`.`wishlist`
WHERE `wishlist_id`
NOT IN (SELECT `wishlist_id` FROM `wam1_nonprod_delta`.`wishlist`);
/** End - Delete rows from wishlist table in M2 those are not available in M1 wishlist table **/

/************************************************************************
-- END: DELTA MIGRATION - Wishlist
*************************************************************************/



/************************************************************************
-- START: DELTA MIGRATION - My Catalog
*************************************************************************/

/*************************/
-- Take backup of perficient_customer_gallery_catalog and perficient_customer_gallery_catalog_page tables
/*************************/

/*
Replace wendover_m2_live DB name with actual M2 migrated DB name
Replace wam1_nonprod_delta DB name with actual M1 delta DB name
 */
DELETE FROM `wendover_m2_live`.`perficient_customer_gallery_catalog`;

DELETE FROM `wendover_m2_live`.`perficient_customer_gallery_catalog_page`;

DELETE wcgc FROM `wam1_nonprod_delta`.wendover_customer_gallery_catalog AS wcgc
LEFT JOIN `wendover_m2_live`.wishlist AS wl ON wl.wishlist_id = wcgc.wishlist_id
WHERE wl.wishlist_id IS NULL;

DELETE FROM `wam1_nonprod_delta`.wendover_customer_gallery_catalog WHERE customer_id IS NULL;

DELETE FROM `wam1_nonprod_delta`.`wendover_customer_gallery_catalog_page` WHERE catalog_id IS NULL;

DELETE wcgc FROM `wam1_nonprod_delta`.`wendover_customer_gallery_catalog` AS wcgc
LEFT JOIN `wendover_m2_live`.customer_entity AS ce ON ce.entity_id = wcgc.customer_id
WHERE ce.entity_id IS NULL;

INSERT INTO `wendover_m2_live`.`perficient_customer_gallery_catalog`
(catalog_id, customer_id, wishlist_id, logo_image, catalog_title, `name`,
phone_number, website_url, company_name, additional_info_1, additional_info_2, price_on, created_at, updated_at)
SELECT catalog_id, customer_id, wishlist_id, logo_image, catalog_title, `name`,
phone_number, website_url, company_name, additional_info_1, additional_info_2, price_on, IFNULL(created_date, NOW()), IFNULL(updated_date, NOW())
FROM `wam1_nonprod_delta`.wendover_customer_gallery_catalog;

DELETE wcgc FROM wam1_nonprod_delta.`wendover_customer_gallery_catalog_page` AS wcgc
LEFT JOIN `wendover_m2_live`.`perficient_customer_gallery_catalog` AS pcgc ON pcgc.catalog_id = wcgc.catalog_id
WHERE pcgc.catalog_id IS NULL;

UPDATE `wendover_m2_live`.`perficient_customer_gallery_catalog` SET logo_image = CONCAT("/", logo_image)
WHERE logo_image IS NOT NULL AND logo_image != "";

INSERT INTO `wendover_m2_live`.`perficient_customer_gallery_catalog_page`
(catalog_id, page_template_id, drop_spot_config, page_position, created_at, updated_at)
SELECT catalog_id, page_template_id, drop_spot_config, page_position, NOW(), NOW()
FROM `wam1_nonprod_delta`.`wendover_customer_gallery_catalog_page`;
/************************************************************************
-- END: DELTA MIGRATION - My Catalog
*************************************************************************/

/**
 * ##################################################
 * ########### Delta Migration Ends Here ############
 * ##################################################
 */
