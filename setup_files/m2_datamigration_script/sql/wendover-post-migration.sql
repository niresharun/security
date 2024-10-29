/*******************************************************************/
-- Confirm first with Sandeep M. before executing below update query
/*******************************************************************/

/************ Disabled cropped images from frontend **************************/
UPDATE `catalog_product_entity_media_gallery_value` SET disabled = 1 WHERE label = 'Cropped';

/************ Delete All Existing Images from 'Frame' & 'Mat' attribute set products  **************************/
DELETE mg.* FROM `catalog_product_entity_media_gallery` mg
INNER JOIN `catalog_product_entity_media_gallery_value_to_entity` mgte ON mgte.value_id = mg.value_id
INNER JOIN `catalog_product_entity` p ON p.row_id = mgte.row_id
INNER JOIN `eav_attribute_set` eas ON p.attribute_set_id = eas.attribute_set_id AND eas.attribute_set_name IN ('Frame', 'Mat')

/*** Update: product is_quick_ship = 1 when product qty > 1   ***/
INSERT INTO catalog_product_entity_int (`attribute_id`, `store_id`, `value`, `row_id`)
SELECT ea.attribute_id, 0, IF(cist.qty > 0, 1, 0) AS attribute_value, cpe.row_id FROM catalog_product_entity cpe
INNER JOIN `cataloginventory_stock_item` cist ON cist.product_id = cpe.entity_id
INNER JOIN `eav_attribute` ea ON ea.attribute_code = 'is_quick_ship'
ON DUPLICATE KEY UPDATE attribute_id = ea.attribute_id, store_id = 0, row_id = cpe.row_id, VALUE = IF(cist.qty > 0, 1, 0);


/********* DO NOT RUN  Disable backorder for product having qty > 0 *******/
/* UPDATE `cataloginventory_stock_item` SET backorders = 0, use_config_backorders = 0 WHERE qty > 0; */

/*******************************************************************/
-- End: Confirm first with Sandeep M. before executing below update query
/*******************************************************************/

/* Delete entries from customer_form_attribute table from non-required customer attribute */
DELETE cfad FROM `customer_form_attribute` AS cfad
INNER JOIN `customer_form_attribute` AS cfa ON cfa.attribute_id = cfad.attribute_id
INNER JOIN eav_attribute AS eav ON eav.attribute_id = cfa.attribute_id
WHERE eav.entity_type_id = 1
AND eav.attribute_code IN (
  "annual_revenue", "website", "tax_id", "percent_of_design", "no_of_designers", "des_comm", "designer_type",
  "mark_pos", "no_of_jobs_per_year", "sq_ft_per_store", "no_of_stores", "business_info", "company"
)
AND cfad.form_code IN ("customer_account_create", "customer_account_edit", "checkout_register");

/**
 Start: To fix issue where customer M1 custom attributes are not saving from admin panel
 Also it is not getting fetched as custom attribute against customer
*/
INSERT INTO customer_form_attribute
SELECT "adminhtml_customer", attribute_id FROM eav_attribute
WHERE attribute_code = "sales_rep"
AND entity_type_id = 1
ON DUPLICATE KEY UPDATE attribute_id = VALUES(attribute_id);

INSERT INTO customer_form_attribute
SELECT "adminhtml_customer", attribute_id FROM eav_attribute
WHERE attribute_code = "user_actual_parent_id"
AND entity_type_id = 1
ON DUPLICATE KEY UPDATE attribute_id = VALUES(attribute_id);

UPDATE customer_eav_attribute
SET is_system = 0
WHERE attribute_id IN (
  SELECT attribute_id FROM eav_attribute
  WHERE attribute_code IN (
    "mark_pos", "designer_type","des_comm", "no_of_designers", "percent_of_design", "price_multiplier", "price_switch",
    "customer_activated", "tax_id", "sales_rep", "website", "annual_revenue", "is_customer_of", "uuid", "is_vip",
    "source_id", "syspro_customer_id", "business_info", "sq_ft_per_store"
  )
  AND entity_type_id = 1
);

INSERT INTO `eav_entity_attribute` (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order)
SELECT 1, 1, 1, attribute_id, 810 AS sort_order FROM eav_attribute WHERE attribute_code IN ("mark_pos", "designer_type","des_comm", "no_of_designers",
"percent_of_design", "price_multiplier", "price_switch", "customer_activated", "tax_id", "sales_rep", "website",
"annual_revenue", "is_customer_of", "uuid", "is_vip", "source_id", "syspro_customer_id", "business_info", "sq_ft_per_store")
AND entity_type_id = 1
ON DUPLICATE KEY UPDATE `sort_order` = VALUES(`sort_order`);
/* End: To fix issue where customer M1 custom attributes are not saving from admin panel */

/* To fix customer save issue while company creation */
UPDATE `customer_eav_attribute` AS cea
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
SET validate_rules = NULL
WHERE ea.attribute_code = "tax_id"
AND ea.entity_type_id = 1;

/* Start: fix to show custom address attributes in different form */
/*INSERT INTO customer_form_attribute
SELECT "adminhtml_customer_address", eava.attribute_id
FROM eav_attribute AS eava
WHERE eava.entity_type_id = 2 AND eava.is_user_defined = 1
ON DUPLICATE KEY UPDATE attribute_id = VALUES(attribute_id);

INSERT INTO customer_form_attribute
SELECT "customer_address_edit", eava.attribute_id
FROM eav_attribute AS eava
WHERE eava.entity_type_id = 2 AND eava.is_user_defined = 1
ON DUPLICATE KEY UPDATE attribute_id = VALUES(attribute_id);

INSERT INTO customer_form_attribute
SELECT "customer_register_address", eava.attribute_id
FROM eav_attribute AS eava
WHERE eava.entity_type_id = 2 AND eava.is_user_defined = 1
ON DUPLICATE KEY UPDATE attribute_id = VALUES(attribute_id);*/
/* End: fix to show custom address attributes in different form */

/* Start: update price multiplier value for all customers in M2 */
UPDATE customer_entity_varchar AS cev
INNER JOIN eav_attribute AS eava ON eava.attribute_id = cev.attribute_id
SET cev.`value` = CASE
		WHEN cev.`value` = 168 THEN "1.00"
		WHEN cev.`value` = 169 THEN "1.25"
		WHEN cev.`value` = 170 THEN "1.50"
		WHEN cev.`value` = 171 THEN "1.75"
		WHEN cev.`value` = 172 THEN "2.00"
		WHEN cev.`value` = 203 THEN "2.25"
		WHEN cev.`value` = 204 THEN "2.50"
		WHEN cev.`value` = 205 THEN "2.75"
		WHEN cev.`value` = 206 THEN "3.00"
		WHEN cev.`value` = 207 THEN "3.25"
		WHEN cev.`value` = 208 THEN "3.50"
		WHEN cev.`value` = 209 THEN "3.75"
		WHEN cev.`value` = 202 THEN "4.00"
	END
WHERE eava.attribute_code = "price_multiplier"
  AND eava.entity_type_id = 1
  AND cev.`value` IS NOT NULL;

-- In above query, updated the price multiplier values based on M2
-- So now, delete the options which are migrated from M1
DELETE eao FROM `eav_attribute_option` AS eao
INNER JOIN eav_attribute AS eava ON eava.attribute_id = eao.attribute_id
WHERE eava.attribute_code = "price_multiplier"
	AND eava.entity_type_id = 1;

-- WENDM2-539 round off custom price multiplier and assign it to
-- price_multiplier attribute of customer
UPDATE customer_entity_varchar AS cev
INNER JOIN eav_attribute AS eava ON eava.attribute_id = cev.attribute_id
INNER JOIN grandriver_cc_customers AS gcc ON gcc.customer_id = cev.entity_id
SET cev.`value` = CASE
		WHEN gcc.custom_price_multiplier <= 1.00 THEN "1.00"
		WHEN gcc.custom_price_multiplier > 1.00 AND gcc.custom_price_multiplier <= 1.25 THEN "1.25"
		WHEN gcc.custom_price_multiplier > 1.25 AND gcc.custom_price_multiplier <= 1.50 THEN "1.50"
		WHEN gcc.custom_price_multiplier > 1.50 AND gcc.custom_price_multiplier <= 1.75 THEN "1.75"
		WHEN gcc.custom_price_multiplier > 1.75 AND gcc.custom_price_multiplier <= 2.00 THEN "2.00"
		WHEN gcc.custom_price_multiplier > 2.00 AND gcc.custom_price_multiplier <= 2.25 THEN "2.25"
		WHEN gcc.custom_price_multiplier > 2.25 AND gcc.custom_price_multiplier <= 2.50 THEN "2.50"
		WHEN gcc.custom_price_multiplier > 2.50 AND gcc.custom_price_multiplier <= 2.75 THEN "2.75"
		WHEN gcc.custom_price_multiplier > 2.75 AND gcc.custom_price_multiplier <= 3.00 THEN "3.00"
		WHEN gcc.custom_price_multiplier > 3.00 AND gcc.custom_price_multiplier <= 3.25 THEN "3.25"
		WHEN gcc.custom_price_multiplier > 3.25 AND gcc.custom_price_multiplier <= 3.50 THEN "3.50"
		WHEN gcc.custom_price_multiplier > 3.50 AND gcc.custom_price_multiplier <= 3.75 THEN "3.75"
		WHEN gcc.custom_price_multiplier > 3.75 AND gcc.custom_price_multiplier <= 4.00 THEN "4.00"
		WHEN gcc.custom_price_multiplier > 4.00 THEN "4.00"
	END
WHERE eava.attribute_code = "price_multiplier"
  AND eava.entity_type_id = 1
  AND gcc.custom_price_multiplier IS NOT NULL
  AND gcc.custom_price_multiplier != 0.00;
/* End: update price multiplier value for all customers in M2 */

/* Start: convert sales_rep attribute from dropdown to text */
UPDATE `eav_attribute` SET backend_type = "varchar", frontend_input = "text", source_model = NULL
WHERE attribute_code = "sales_rep";

INSERT INTO customer_entity_varchar (attribute_id, entity_id, `value`)
SELECT cei.attribute_id, cei.entity_id, eaov.value FROM customer_entity_int AS cei
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cei.attribute_id
INNER JOIN `eav_attribute_option_value` AS eaov ON eaov.option_id = cei.value
WHERE ea.attribute_code = "sales_rep"
AND ea.entity_type_id = 1
AND cei.`value` IS NOT NULL
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

DELETE cei FROM customer_entity_int AS cei
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cei.attribute_id
WHERE ea.attribute_code = "sales_rep"
AND ea.entity_type_id = 1;

DELETE eao FROM eav_attribute_option AS eao
INNER JOIN eav_attribute AS ea ON ea.attribute_id = eao.attribute_id
WHERE ea.attribute_code = "sales_rep"
AND ea.entity_type_id = 1;
/* End: convert sales_rep attribute from dropdown to text */

/* Start: attribute values which are not required from M1 in M2 */
DELETE cev FROM customer_entity_varchar AS cev
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cev.attribute_id
WHERE ea.attribute_code = "type_of_projects"
AND ea.entity_type_id = 1;

DELETE cev FROM customer_entity_varchar AS cev
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cev.attribute_id
WHERE ea.attribute_code = "no_of_jobs_per_year"
AND ea.entity_type_id = 1;
/* End: attribute values which are not required from M1 in M2 */

/* Start: convert type_of_projects attribute from textarea to dropdown */
/* Revisit this logic */
UPDATE `eav_attribute` SET frontend_input = "select" WHERE attribute_code = "type_of_projects";

-- There are options of attribute type_of_projects which are present but not required
DELETE eao FROM eav_attribute_option AS eao
INNER JOIN eav_attribute AS ea ON ea.attribute_id = eao.attribute_id
WHERE ea.attribute_code = "type_of_projects"
AND ea.entity_type_id = 1;
/* End: convert type_of_projects attribute from textarea to dropdown */

/* Start: set required product attributes as searchable, filterable and visible */
/*UPDATE `catalog_eav_attribute` AS cea
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
SET cea.is_filterable = 1, cea.is_filterable_in_search = 1
WHERE ea.attribute_code IN ("color","filter_size","category_list","licensed_collection","lifestyle","simplified_size","simplified_medium")
AND ea.entity_type_id = 4;
*/
UPDATE `catalog_eav_attribute` AS cea
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
SET cea.is_filterable = 1, cea.is_filterable_in_search = 1
WHERE ea.attribute_code IN ("color","filter_size","category_list","licensed_collection","lifestyle","simplified_size","simplified_medium","orientation","price","color_frame","color_mat","color_family","color_family_frame","color_family_mat","art_category")
AND ea.entity_type_id = 4;

UPDATE `catalog_eav_attribute` AS cea
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
SET cea.is_filterable = 0, cea.is_filterable_in_search = 0
WHERE ea.attribute_code NOT IN ("color","filter_size","category_list","licensed_collection","lifestyle","simplified_size","simplified_medium","orientation","price","color_frame","color_mat","color_family","color_family_frame","color_family_mat")
AND ea.entity_type_id = 4;

/*UPDATE `catalog_eav_attribute` AS cea
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
SET cea.is_searchable = 1
WHERE ea.attribute_code IN ("color","medium","specialty","filter_size","category_list","keyword_list","licensed_collection","lifestyle","orientation","simplified_size","simplified_medium","color_frame","color_mat")
AND ea.entity_type_id = 4;*/
UPDATE `catalog_eav_attribute` AS cea
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
SET cea.is_searchable = 1
WHERE ea.attribute_code IN ("color","medium","specialty","filter_size","category_list","keyword_list","licensed_collection","lifestyle","orientation","simplified_size","simplified_medium","color_frame","color_mat","name","sku","licensee_collection","color_family_frame","color_family_mat")
AND ea.entity_type_id = 4;

UPDATE `catalog_eav_attribute` AS cea
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
SET cea.is_searchable = 0
WHERE ea.attribute_code NOT IN ("color","medium","specialty","filter_size","category_list","keyword_list","licensed_collection","lifestyle","orientation","simplified_size","simplified_medium","color_frame","color_mat","name","sku","licensee_collection","color_family_frame","color_family_mat")
AND ea.entity_type_id = 4;

UPDATE `catalog_eav_attribute` AS cea
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
SET cea.is_visible_on_front = 1
WHERE ea.attribute_code IN ("top_mat_width","bottom_mat_width","item_height","item_width","MEDIUM","specialty","mirror_bevel","default_item_price","frames","mats","frame_default_sku","bottom_mat_default_sku","top_mat_default_sku","liner_sku","filter_size","bottom_mat_size_bottom","bottom_mat_size_left","bottom_mat_size_right","bottom_mat_size_top","category_list","licensed_collection","lifestyle","orientation","other_skus_in_series","top_mat_size_bottom","top_mat_size_left","top_mat_size_right","top_mat_size_top","simplified_size","simplified_medium","frame_width","color_frame","color_mat","frame_depth","liner_width","frame_type","corner1_img","length1_img","corner2_img","length2_img","lifestyle_image","cropped","mat_pattern")
AND ea.entity_type_id = 4;

UPDATE `catalog_eav_attribute` AS cea
INNER JOIN eav_attribute AS ea ON ea.attribute_id = cea.attribute_id
SET cea.is_visible_on_front = 0
WHERE ea.attribute_code IN ("price","color","top_mat_width","bottom_mat_width","item_height","item_width","medium","specialty","glass_width","glass_height","product_customizer","licensed_collection","lifestyle","orientation","simplified_medium","simplified_size")
AND ea.entity_type_id = 4;
/* End: set required product attributes as searchable, filterable and visible */

/* Start: To fix issue while company creation(migration) */
UPDATE eav_attribute SET is_required = 0
WHERE entity_type_id = 1
AND attribute_code IN ("business_info", "tax_id", "company");
/* End: To fix issue while company creation(migration) */

/* Start: To resolve the issue of blank category(migrated from M1) page */
UPDATE catalog_category_entity_varchar
SET `value` = '1column'
WHERE `value` = '1'
AND attribute_id IN (
    SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'page_layout'
);

UPDATE catalog_category_entity_varchar SET `value` = "1column"
WHERE row_id = 3 AND attribute_id = (
    SELECT attribute_id FROM eav_attribute WHERE attribute_code = "page_layout" AND entity_type_id = 3 -- our product(p) category
);

UPDATE catalog_category_entity_varchar AS ccev
INNER JOIN catalog_category_entity AS cce ON cce.row_id = ccev.row_id
INNER JOIN eav_attribute AS eav ON eav.attribute_id = ccev.attribute_id
SET ccev.value = "PAGE"
WHERE eav.attribute_code = "display_mode"
AND cce.level = 3
AND children_count > 0;

UPDATE catalog_category_entity_varchar AS ccev
INNER JOIN catalog_category_entity AS cce ON cce.row_id = ccev.row_id
INNER JOIN eav_attribute AS eav ON eav.attribute_id = ccev.attribute_id
SET ccev.value = "1column"
WHERE eav.attribute_code = "page_layout"
AND cce.level = 3
AND children_count > 0;

UPDATE catalog_category_entity_varchar AS ccev
INNER JOIN catalog_category_entity AS cce ON cce.row_id = ccev.row_id
INNER JOIN eav_attribute AS eav ON eav.attribute_id = ccev.attribute_id
SET ccev.value = "PRODUCTS"
WHERE eav.attribute_code = "display_mode"
AND cce.level = 4;

UPDATE catalog_category_entity_varchar AS ccev
INNER JOIN catalog_category_entity AS cce ON cce.row_id = ccev.row_id
INNER JOIN eav_attribute AS eav ON eav.attribute_id = ccev.attribute_id
SET ccev.value = "2columns-left"
WHERE eav.attribute_code = "page_layout"
AND cce.level = 4;
/* End: To resolve the issue of blank category(migrated from M1) page */

/* set website id for customer */
UPDATE `customer_entity` SET website_id = 1 WHERE website_id = 0;

/* Start: My Catalog Migration */
DELETE FROM `perficient_customer_gallery_catalog`;

DELETE FROM `perficient_customer_gallery_catalog_page`;

DELETE wcgc FROM wendover_customer_gallery_catalog AS wcgc
LEFT JOIN wishlist AS wl ON wl.wishlist_id = wcgc.wishlist_id
WHERE wl.wishlist_id IS NULL;

DELETE FROM wendover_customer_gallery_catalog WHERE customer_id IS NULL;

DELETE FROM `wendover_customer_gallery_catalog_page` WHERE catalog_id IS NULL;

INSERT INTO `perficient_customer_gallery_catalog`
(catalog_id, customer_id, wishlist_id, logo_image, catalog_title, `name`, 
phone_number, website_url, company_name, additional_info_1, additional_info_2, price_on, created_at, updated_at)
SELECT catalog_id, customer_id, wishlist_id, logo_image, catalog_title, `name`, 
phone_number, website_url, company_name, additional_info_1, additional_info_2, price_on, IFNULL(created_date, NOW()), IFNULL(updated_date, NOW())
FROM wendover_customer_gallery_catalog;

DELETE wcgc FROM `wendover_customer_gallery_catalog_page` AS wcgc
LEFT JOIN `perficient_customer_gallery_catalog` AS pcgc ON pcgc.catalog_id = wcgc.catalog_id
WHERE pcgc.catalog_id IS NULL;

UPDATE `perficient_customer_gallery_catalog` SET logo_image = CONCAT("/", logo_image) 
WHERE logo_image IS NOT NULL AND logo_image != "";

INSERT INTO `perficient_customer_gallery_catalog_page`
(catalog_id, page_template_id, drop_spot_config, page_position, created_at, updated_at)
SELECT catalog_id, page_template_id, drop_spot_config, page_position, NOW(), NOW()
FROM `wendover_customer_gallery_catalog_page`;
/* End: My Catalog Migration */

/* Need to create Wendover Parent company */
/*INSERT INTO `company`
(`status`, company_name, legal_name, company_email, street, city, country_id, region_id, region, postcode, telephone)
VALUES (1, 'Wendover Company', 'Wendover Company', 'company@wendover.com', 'West Wendover', 'Nevada', 'US', 2, '', 89883, 0123456789);

INSERT INTO `company_advanced_customer_entity` VALUES (1, 1, NULL, 1, NULL);

INSERT INTO `company_credit` (company_id, credit_limit, balance, currency_code, exceed_limit) VALUES (1, NULL, 0.0000, 'USD', 0);

INSERT INTO `company_payment` VALUES (1, 0, NULL, 1);

INSERT INTO `company_structure` (parent_id, entity_id, entity_type, path, `position`, `level`) VALUES
(0, 1, 0, 1, 0, 0);

INSERT INTO `company_roles` (sort_order, role_name, company_id)
VALUES (0, "Customer's Customer", 1), (0, 'Customer Employee', 1);

INSERT INTO `company_permissions` (role_id, resource_id, permission)
SELECT 1, resource_id, permission FROM `perficient_company_templates`
WHERE role_id = 1;

INSERT INTO `company_permissions` (role_id, resource_id, permission)
SELECT 2, resource_id, permission FROM `perficient_company_templates`
WHERE role_id = 2;*/
/* Need to create Wendover Parent company */

/* START: Below statements are for designers as company and employee migration */
-- First take backup of grandriver_cc_designers table
ALTER TABLE `grandriver_cc_customers` ADD `import_status` INT DEFAULT NULL AFTER designer;

UPDATE `grandriver_cc_customers` SET import_status = 0;

TRUNCATE TABLE `grandriver_cc_designers`;

ALTER TABLE `grandriver_cc_designers` ADD `cleaned_telephone` VARCHAR(255) DEFAULT NULL AFTER telephone;

ALTER TABLE `grandriver_cc_designers` ADD `import_status` INT DEFAULT NULL AFTER email;

ALTER TABLE `grandriver_cc_designers` ADD `telephone_count` INT DEFAULT NULL AFTER email;

INSERT INTO `grandriver_cc_designers` (customer_id, email, telephone_count, import_status, company_name,
telephone, address_line_1, city, state, postal_code)
SELECT ce.entity_id AS customer_id, ce.email, NULL AS telephone_count, 0, TRIM(cev.value) AS company_name,
cae.telephone, cae.street AS address_line_1, cae.city, cae.region_id, cae.postcode AS postal_code
FROM customer_entity AS ce
LEFT JOIN `grandriver_cc_customers` AS gcc ON gcc.customer_id = ce.entity_id
INNER JOIN eav_attribute AS eav ON eav.attribute_code = "company" AND eav.entity_type_id = 1
LEFT JOIN customer_entity_varchar AS cev ON cev.entity_id = ce.entity_id AND cev.attribute_id = eav.attribute_id
LEFT JOIN customer_address_entity AS cae ON cae.entity_id = ce.default_billing
WHERE gcc.customer_id IS NULL;

UPDATE `grandriver_cc_designers` SET import_status = 0;

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

ALTER TABLE `grandriver_cc_designers` ADD `cleaned_street` VARCHAR(255) DEFAULT NULL AFTER fax;

UPDATE `grandriver_cc_designers` SET cleaned_street = SUBSTRING(REPLACE(LOWER(TRIM(address_line_1)), " ", ""), 1, 10)
WHERE address_line_1 IS NOT NULL;

ALTER TABLE `grandriver_cc_designers` ADD `cleaned_company_name` VARCHAR(255) DEFAULT NULL AFTER company_name;

UPDATE `grandriver_cc_designers` SET cleaned_company_name = LOWER(TRIM(company_name))
WHERE company_name IS NOT NULL;

UPDATE `grandriver_cc_designers` SET cleaned_company_name = REPLACE(cleaned_company_name, '"', "'");

ALTER TABLE `grandriver_cc_designers` ADD `company_name_count` INT DEFAULT NULL AFTER cleaned_company_name;

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
-- End: Prepare delete query for invalid address

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

-- To fix issue of customer save during company creation
/**
Before executing below update query, verify that option id 1 and 2 is of gender attribute
SELECT * FROM eav_attribute WHERE attribute_code = "gender";
SELECT * FROM `eav_attribute_option` WHERE attribute_id = <gender_attribute_id>;
SELECT * FROM `eav_attribute_option_value` WHERE option_id IN (1, 2);
 */
UPDATE `eav_attribute_option_value` SET `value` = "Male" WHERE option_id = 1;
UPDATE `eav_attribute_option_value` SET `value` = "Female" WHERE option_id = 2;

/* END: Below statements are for designers as company and employee migration */








