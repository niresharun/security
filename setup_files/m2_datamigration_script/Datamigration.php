<?php
/**
 * Module to migrate M1 data to M2
 *
 * @category: PHP
 * @package: Perficient/DataMigrationShell
 * @copyright:
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suraj Jaiswal <suraj.jaiswal@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_DataMigrationShell
 */

$DOCUMENT_ROOT = './';

$inputVariables = array();
parse_str($_SERVER['argv'][1], $inputVariables);

if (count($inputVariables) <= 0 ) {
    echo 'Error: Invalid input.';
    exit(1);
}

/** Start: Mapping config.xml file */
$fname = $DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/1.12.0.2/config.xml.dist";
$xml   = simplexml_load_file($fname);
// Providing source DB Details
unset($xml->source->database);
$source = $xml->source->addChild("database");
$source->addAttribute('host', $inputVariables['M1_DB_HOST']);
$source->addAttribute('name', $inputVariables['M1_DB_NAME']);
$source->addAttribute('user', $inputVariables['M1_DB_USER']);
$source->addAttribute('password', $inputVariables['M1_DB_PASS']);
// Providing destination DB Details
unset($xml->destination->database);
$destination = $xml->destination->addChild("database");
$destination->addAttribute('host', $inputVariables['M2_DB_HOST']);
$destination->addAttribute('name', $inputVariables['M2_DB_NAME']);
$destination->addAttribute('user', $inputVariables['M2_DB_USER']);
$destination->addAttribute('password', $inputVariables['M2_DB_PASS']);
unset($xml->options->map_file);
$xml->options->addChild("map_file", 'etc/commerce-to-commerce/1.12.0.2/map.xml');
unset($xml->options->eav_map_file);
$xml->options->addChild("eav_map_file", 'etc/commerce-to-commerce/map-eav.xml');
unset($xml->options->eav_attribute_groups_file);
$xml->options->addChild("eav_attribute_groups_file", 'etc/commerce-to-commerce/eav-attribute-groups.xml');
unset($xml->options->settings_map_file);
$xml->options->addChild("settings_map_file", 'etc/commerce-to-commerce/settings.xml');
unset($xml->options->sales_order_map_file);
$xml->options->addChild("sales_order_map_file", 'etc/commerce-to-commerce/map-sales.xml');
// set bulk size
unset($xml->options->bulk_size);
$xml->options->addChild("bulk_size", '1000');
// Providing source hash crptography Details
unset($xml->options->crypt_key);
$xml->options->addChild("crypt_key", '8b316770bc70688cdd50f171366d8109');
$xml->asXML($DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/1.12.0.2/config.xml");
/** End: Mapping config.xml file */

/** Start: Mapping the tables and custom field map.xml */
$fname = $DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/1.12.0.2/map.xml.dist";
$xml   = simplexml_load_file($fname);
$documentmap = array('log_visitor','cms_block','cms_block_store','cms_page','cms_page_store',
    'enterprise_cms_hierarchy_lock','enterprise_cms_hierarchy_metadata','enterprise_cms_hierarchy_node',
    'enterprise_cms_increment','enterprise_cms_page_revision','enterprise_cms_page_version'
);
foreach($documentmap as $val){
    $ignore = $xml->source->document_rules->addChild("ignore");
    $ignore->addChild("document", htmlspecialchars("$val"));
}
$documentmap = array(
    'am_label','amasty_ccpa_action_log','amasty_ccpa_consent_log','amasty_ccpa_consent_queue',
    'amasty_ccpa_consents','amasty_ccpa_consents_scope','amasty_ccpa_delete_request','amasty_ccpa_privacy_policy',
    'amasty_ccpa_privacy_policy_content','amasty_gdpr_action_log','amasty_gdpr_consent_log','amasty_gdpr_consent_queue',
    'amasty_gdpr_consents','amasty_gdpr_consents_scope','amasty_gdpr_delete_request','amasty_gdpr_privacy_policy',
    'amasty_gdpr_privacy_policy_content','amasty_gdprcookie_cookie','amasty_gdprcookie_cookie_consents',
    'amasty_gdprcookie_cookie_description','amasty_gdprcookie_cookie_group_description',
    'amasty_gdprcookie_cookie_group_link','amasty_gdprcookie_group_cookie','amasty_geoip_block','amasty_geoip_block_v6',
    'amasty_geoip_location','amasty_label_cl','amasty_label_index','amasty_label_main_cl','amasty_menu_item_content',
    'amasty_menu_item_order','amasty_menu_link','catalogpermissions_category_cl','catalogpermissions_product_cl',
    'catalogrule_product__temp00c542b7','catalogrule_product__temp06f68c1f','catalogrule_product__temp09b0e1c5',
    'catalogrule_product__temp0ecf2f0c','catalogrule_product__temp0ff020e3','catalogrule_product__temp11eafaaf',
    'catalogrule_product__temp14610c1a','catalogrule_product__temp17fd1396','catalogrule_product__temp19493666',
    'catalogrule_product__temp1e0bee53','catalogrule_product__temp1f990c0f','catalogrule_product__temp21433fa5',
    'catalogrule_product__temp255237f9','catalogrule_product__temp266f08ed','catalogrule_product__temp2991e710',
    'catalogrule_product__temp29e7383e','catalogrule_product__temp2e420b22','catalogrule_product__temp2ec97cc8',
    'catalogrule_product__temp2f20c186','catalogrule_product__temp2fd38c38','catalogrule_product__temp34d6a34d',
    'catalogrule_product__temp39e85b4a','catalogrule_product__temp44a724d8','catalogrule_product__temp4c4f802e',
    'catalogrule_product__temp56e5796a','catalogrule_product__temp579f95ba','catalogrule_product__temp592964da',
    'catalogrule_product__temp5cf916fc','catalogrule_product__temp5d4e1294','catalogrule_product__temp656590e5',
    'catalogrule_product__temp6fccce1f','catalogrule_product__temp70611c1f','catalogrule_product__temp73eaa64c',
    'catalogrule_product__temp7a506558','catalogrule_product__temp7f2f3a09','catalogrule_product__temp80ffe36e',
    'catalogrule_product__temp8128386f','catalogrule_product__temp863d7186','catalogrule_product__temp892442ac',
    'catalogrule_product__temp8a2d03c1','catalogrule_product__temp92709243','catalogrule_product__temp9721156c',
    'catalogrule_product__temp99ce55ec','catalogrule_product__temp9a2f5a6d','catalogrule_product__temp9c67e529',
    'catalogrule_product__temp9ed57120','catalogrule_product__tempa280d47a','catalogrule_product__tempa4a666cf',
    'catalogrule_product__tempa79bf0b3','catalogrule_product__tempb38f18a8','catalogrule_product__tempb7ea0b26',
    'catalogrule_product__tempbf888585','catalogrule_product__tempc1dbaecd','catalogrule_product__tempc578b1c8',
    'catalogrule_product__tempc9dda5b9','catalogrule_product__tempd8d3da4e','catalogrule_product__tempdad5367e',
    'catalogrule_product__tempdccc2a98','catalogrule_product__tempdfb35dd5','catalogrule_product__tempe61ea479',
    'catalogrule_product__tempeb9bd3af','catalogrule_product__tempee5d9fd1','catalogrule_product__tempf6290d1a',
    'catalogrule_product__tempff0221bd','email_b2b_quote','inventory_cl','mageworx_seobase_custom_canonical',
    'mageworx_seocrosslinks_crosslink','mageworx_seocrosslinks_crosslink_store','mageworx_seoextended_category',
    'mageworx_seoredirects_redirect_custom','mageworx_seoredirects_redirect_dp','mageworx_seoreports_category',
    'mageworx_seoreports_page','mageworx_seoreports_product','mageworx_seoxtemplates_template_brand',
    'mageworx_seoxtemplates_template_category','mageworx_seoxtemplates_template_categoryfilter',
    'mageworx_seoxtemplates_template_landingpage','mageworx_seoxtemplates_template_product',
    'mageworx_seoxtemplates_template_relation_attributeset','mageworx_seoxtemplates_template_relation_brand',
    'mageworx_seoxtemplates_template_relation_category','mageworx_seoxtemplates_template_relation_categoryfilter',
    'mageworx_seoxtemplates_template_relation_landingpage','mageworx_seoxtemplates_template_relation_product',
    'mailchimp_errors','mailchimp_interest_group','mailchimp_stores','mailchimp_sync_batches',
    'mailchimp_sync_ecommerce','mailchimp_webhook_request','paradoxlabs_stored_card','perficient_company_roles',
    'perficient_company_templates','perficient_customer_catalog_share','perficient_customer_catalog_template',
    'perficient_customer_gallery_catalog','perficient_customer_gallery_catalog_page','purchase_order',
    'purchase_order_applied_rule','purchase_order_applied_rule_approver','purchase_order_approved_by',
    'purchase_order_comment','purchase_order_company_config','purchase_order_log','purchase_order_rule',
    'purchase_order_rule_applies_to','purchase_order_rule_approver','salesrule_rule_cl','sequence_purchase_order_0',
    'sequence_purchase_order_1','targetrule_product_rule_cl','targetrule_rule_product_cl',
    'am_customform_answer','am_customform_form','amasty_amshopby_cms_page',
    'amasty_amshopby_filter_setting','amasty_amshopby_group_attr','amasty_amshopby_group_attr_option',
    'amasty_amshopby_group_attr_value','amasty_amshopby_option_setting','amasty_amshopby_page',
    'amasty_amshopby_page_store','perficient_logging_event','perficient_logging_event_changes',
    'perficient_mydisplayinformation'
);
foreach($documentmap as $val){
    $ignore = $xml->destination->document_rules->addChild("ignore");
    $ignore->addChild("document", htmlspecialchars("$val"));
}
$fieldmap = array(
    'catalog_eav_attribute.layered_navigation_canonical','sales_flat_order_item.itemcomment',
    'sales_flat_order_item.quick_ship','sales_flat_order_payment.secure_payment_data',
    'sales_flat_quote_payment.secure_payment_data','sales_flat_quote.quick_ship','sales_flat_quote_item.itemcomment',
    'sales_flat_quote_item.quick_ship','sales_flat_quote_item.custom_rule_applied'
);
foreach($fieldmap as $val){
    $ignore = $xml->source->field_rules->addChild("ignore");
    $ignore->addChild("field", htmlspecialchars("$val"));
}
$fieldmap = array(
    'catalog_eav_attribute.layered_navigation_canonical','adminnotification_inbox.is_amasty',
    'adminnotification_inbox.expiration_date','adminnotification_inbox.image_url','cms_page.meta_robots',
    'cms_page.mageworx_hreflang_identifier','cms_page.in_html_sitemap','cms_page.use_in_crosslinking',
    'cms_page.in_xml_sitemap','quote.mailchimp_abandonedcart_flag','quote.mailchimp_campaign_id',
    'quote.mailchimp_landing_page','quote_address.order_shipping_notes','quote_address.receiver_name',
    'quote_address.receiver_telephone','quote_address.receiver_telephone','quote_address.location',
    'quote_address.delivery_appointment','quote_address.loading_dock_available','quote_address.receiving_hours',
    'sales_order_address.order_shipping_notes','sales_order_address.receiver_name',
    'sales_order_address.receiver_telephone','sales_order_address.receiver_telephone','sales_order_address.location',
    'sales_order_address.delivery_appointment','sales_order_address.loading_dock_available',
    'sales_order_address.receiving_hours','quote_payment.tokenbase_id','sales_order_payment.tokenbase_id',
    'sitemap.count_by_entity','sitemap.entity_type','sitemap.server_path','sitemap.sitemap_link',
    'grandriver_cc_customers.import_status','grandriver_cc_designers.telephone_count',
    'grandriver_cc_designers.import_status','grandriver_cc_designers.cleaned_telephone'
);
foreach($fieldmap as $val){
    $ignore = $xml->destination->field_rules->addChild("ignore");
    $ignore->addChild("field", htmlspecialchars("$val"));
}
$xml->asXML($DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/1.12.0.2/map.xml");
/** End: Mapping the tables and custom field map.xml */

/** Start: Mapping the settings.xml */
$fname = $DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/settings.xml.dist";
$xml   = simplexml_load_file($fname);
$pathmap = array(
    'admin/dashboard/enable_charts','design/fallback/fallback','design/search_engine_robots/default_robots',
    'design/head/includes','design/header/logo_src','design/footer/absolute_footer','requirelogin/general/enabled',
    'requirelogin/general/redirect_url','requirelogin/general/redirect_url_type',
    'requirelogin/exceptions/allow_customer_router','requirelogin/exceptions/redirect_exceptions',
    'grandriver_framework/angularjs/enable','grandriver_framework/chromeframe/enable',
    'grandriver_framework/dojo/enable','grandriver_framework/extcore/enable','grandriver_framework/googlemaps/enable',
    'grandriver_framework/jquery/enable','grandriver_framework/jquery/version','grandriver_framework/jquery/noconflict',
    'grandriver_framework/jquery/alias','grandriver_framework/jquery/fallback',
    'grandriver_framework/jquerytools/enable','grandriver_framework/jquerytools/version',
    'grandriver_framework/jquerytools/merge_jquery','grandriver_framework/jquerytools/fallback',
    'grandriver_framework/jqueryui/enable','grandriver_framework/mootools/enable',
    'grandriver_framework/prototype/enable','grandriver_framework/prototype/version',
    'grandriver_framework/prototype/fallback','grandriver_framework/scriptaculous/enable',
    'grandriver_framework/scriptaculous/version','grandriver_framework/scriptaculous/fallback',
    'grandriver_framework/swfobject/enable','grandriver_framework/webfont/enable',
    'grandriver_framework/webfont/version','grandriver_framework/webfont/fonts','grandriver_framework/webfont/fallback',
    'restrictcustomer/general/enabled','restrictcustomer/cart/restrict','restrictcustomer/cart/allowed_customer_groups',
    'restrictcustomer/cart/msg','restrictcustomer/checkout/restrict',
    'restrictcustomer/checkout/allowed_customer_groups','restrictcustomer/checkout/msg',
    'restrictcustomer/catalog/restrict','restrictcustomer/catalog/allowed_customer_groups',
    'restrictcustomer/catalog/msg','restrictcustomer/prices/restrict',
    'restrictcustomer/prices/hide_prices_in_shopping_cart','restrictcustomer/prices/allowed_customer_groups',
    'restrictcustomer/prices/msg','restrictcustomer/details/restrict',
    'restrictcustomer/details/allowed_customer_groups','restrictcustomer/details/msg','carriers/ups/password',
    'customer/create_account/default_group','customer/create_account/email_domain',
    'customer/create_account/email_confirmation_template','customer/create_account/email_confirmed_template',
    'customer/password/forgot_email_template','customer/password/remind_email_template',
    'customer/magento_customerbalance/email_template','customer/address_templates/text',
    'customer/address_templates/oneline','customer/address_templates/html','customer/address_templates/pdf',
    'customer/captcha/enable','customer/customeractivation/admin_email',
    'customer/customeractivation/registration_admin_template','customer/customeractivation/alert_customer',
    'customer/customeractivation/activation_template','customer/customeractivation/activation_status_default',
    'customer/customeractivation/require_activation_for_specific_groups',
    'customer/customeractivation/always_send_admin_email','customer/customeractivation/require_activation_groups',
    'catalog/productalert_cron/error_email_template','dev/aoe_templatehints/templateHintRenderer',
    'dev/aoe_templatehints/enablePhpstormRemoteCall','dev/aoe_templatehints/remoteCallUrlTemplate',
    'web/unsecure/base_renderer_url','grandriver_artrenderer/renderer/base_renderer_url',
    'responseconfiguration/responseconfigs/grid1280','responseconfiguration/responseconfigs/productsperrow',
    'responseconfiguration/responseconfigs/crosssellhorizontal','responseconfiguration/responseconfigs/enablereview',
    'web/secure/base_renderer_url','trans_email/ident_general/name','trans_email/ident_general/email',
    'trans_email/ident_sales/name','trans_email/ident_sales/email','trans_email/ident_support/name',
    'trans_email/ident_support/email','trans_email/ident_custom1/name','trans_email/ident_custom1/email',
    'trans_email/ident_custom2/name','trans_email/ident_custom2/email','sales_email/order_comment/template',
    'sales_email/order_comment/guest_template','sales_email/invoice/template','sales_email/invoice/guest_template',
    'sales_email/invoice_comment/template','sales_email/invoice_comment/guest_template','sales_email/shipment/template',
    'sales_email/shipment/guest_template','sales_email/shipment_comment/template',
    'sales_email/shipment_comment/guest_template','sales_email/creditmemo/template',
    'sales_email/creditmemo/guest_template','sales_email/creditmemo_comment/template',
    'sales_email/creditmemo_comment/guest_template','sales_email/magento_rma/template',
    'sales_email/magento_rma/guest_template','sales_email/magento_rma_auth/template',
    'sales_email/magento_rma_auth/guest_template','sales_email/magento_rma_customer_comment/template',
    'carriers/xshipping/active','carriers/xshipping/name','carriers/xshipping/title','carriers/xshipping/price',
    'carriers/xshipping/specificerrmsg','carriers/xshipping/shipping_message','carriers/xshipping/sallowspecific',
    'carriers/xshipping/specificcountry','catalog/placeholder/image_placeholder',
    'catalog/placeholder/small_image_placeholder','catalog/placeholder/thumbnail_placeholder',
    'google/analytics/account','sales_email/quickship/recipient_email','sales_email/quickship/sender_email_identity',
    'sales_email/quickship/email_template','gri_rest_api/category/mapping','gri_rest_api/product/mapping',
    'gri_rest_api/customer/mapping','gri_rest_api/customer_address/mapping','gri_rest_api/order/mapping_order',
    'gri_rest_api/order/mapping_items','gri_rest_api/order/mapping_address','gri_rest_api/order/mapping_comments',
    'gri_rest_api/wishlist/mapping','gri_rest_api/mycatalog/mapping_templates','gri_rest_api/mycatalog/mapping_catalogs'
);
foreach($pathmap as $val){
    $ignore = $xml->key->addChild("ignore");
    $ignore->addChild("path", htmlspecialchars("$val"));
}
$xml->asXML($DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/settings.xml");
/** End: Mapping the settings.xml */

/** Start: Mapping eav-map.xml file */
$fname = $DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/map-eav.xml.dist";
$xml   = simplexml_load_file($fname);
$fieldmap = array('catalog_eav_attribute.layered_navigation_canonical');
foreach($fieldmap as $val){
    $ignore = $xml->destination->field_rules->addChild("ignore");
    $ignore->addChild("field", htmlspecialchars("$val"));
}
$xml->asXML($DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/map-eav.xml");
/** End: Mapping eav-map.xml file */

/** Start: Mapping eav-attribute-groups.xml file */
$fname = $DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/eav-attribute-groups.xml.dist";
$xml   = simplexml_load_file($fname);
// map for catalog_product type
$attributemap = array('manufacturer','art_mount_type','wag_number','mirror_bevel');
foreach($attributemap as $val){
    $attribute = $xml->group->addChild("attribute", htmlspecialchars("$val"));
    $attribute->addAttribute("type", "catalog_product");
}
// map for catalog_category type
$attributemap = array('packagecreator_is_required','packagecreator_price_type','packagecreator_price_mod_amnt',
    'packagecreator_default_product', 'packagecreator_canstartpackage', 'packagecreator_slot_is_hidden',
    'is_not_customizable', 'show_subcat_listing');
foreach($attributemap as $val){
    $attribute = $xml->group->addChild("attribute", htmlspecialchars("$val"));
    $attribute->addAttribute("type", "catalog_category");
}
// map for customer type
$attributemap = array('syspro_customer_id1');
foreach($attributemap as $val){
    $attribute = $xml->group->addChild("attribute", htmlspecialchars("$val"));
    $attribute->addAttribute("type", "customer");
}
$xml->asXML($DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/eav-attribute-groups.xml");
/** End: Mapping eav-attribute-groups.xml file */


/** Start: Mapping map-sales.xml file */
$fname = $DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/map-sales.xml.dist";
$xml   = simplexml_load_file($fname);
$fieldmap = array(
    'sales_order.is_customized','sales_order.order_source_event','sales_order.order_source_rep','sales_order.uuid',
    'sales_order.quickBooksOrderNumber','sales_order.customerOrderNumber','sales_order.by_new_customer',
    'sales_order.source_name','sales_order.source_id','sales_order.custom_discount_percentage',
    'sales_order.custom_discount_amount','sales_order.print_status',
    'sales_flat_order.is_customized','sales_flat_order.order_source_event','sales_flat_order.order_source_rep',
    'sales_flat_order.uuid','sales_flat_order.quickBooksOrderNumber','sales_flat_order.customerOrderNumber',
    'sales_flat_order.by_new_customer','sales_flat_order.source_name','sales_flat_order.source_id',
    'sales_flat_order.custom_discount_percentage','sales_flat_order.custom_discount_amount',
    'sales_flat_order.print_status','sales_flat_order.quick_ship'
);
foreach($fieldmap as $val) {
    $ignore = $xml->source->field_rules->addChild("ignore");
    $ignore->addChild("field", htmlspecialchars("$val"));
}
$fieldmap = array(
    'sales_order.mailchimp_abandonedcart_flag','sales_order.mailchimp_campaign_id','sales_order.mailchimp_landing_page',
    'sales_order.mailchimp_flag'
);
foreach($fieldmap as $val) {
    $ignore = $xml->destination->field_rules->addChild("ignore");
    $ignore->addChild("field", htmlspecialchars("$val"));
}
$xml->asXML($DOCUMENT_ROOT . "vendor/magento/data-migration-tool/etc/commerce-to-commerce/map-sales.xml");
/** End: Mapping map-sales.xml file */


