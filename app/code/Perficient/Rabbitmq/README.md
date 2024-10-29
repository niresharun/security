Perficient Rabbitmq
====================

Last tested on Magento version 2.4.0

Prerequisite
----
1. Rabbitmq installed on server
2. Rabbitmq credential present in app/etc/env.php file.
3. Rabbitmq connection work properly
4. Rabbitmq management plugin installed 

What I got so far:
-----

1.This module is responsible to send and receive messages from/to rabbitmq.
2.In following cases message are send to rabbitmq
  a. Customer create/update while placing an order from both frontend and admin
  b. Customer Address create/update while placing an order from both frontend and admin
3.In following cases messages are received from rabbitmq
  a. Inventory update
  
Sample Messages For Rabbitmq
-----
a) Inventory update
sudo php bin/magento queue:consumers:start productInventoryUpdateConsumer
sudo php bin/magento queue:message:publish '{"stock":[{"sku":"MS-Champ-L","qty":"1000"},{"sku":"MS-Champ-S","qty":"1000"},{"sku":"MS-Champ-M-2","qty":"1000"}]}' erp.catalog.inventory.update

b) Product Create Update
sudo php bin/magento queue:consumer:start productCreateUpdateConsumer
sudo php bin/magento queue:message:publish '{"product":{"sku":"MS-Champ-L54321","name":"Champ Tee Large 54321","attribute_set":"Default","price":266.98,"status":0,"visibility":"Not Visible Individually","type_id":"simple","weight":"0.09","extension_attributes":{"category_links":[{"position":0,"category":"Default Category/Test Two Category"},{"position":1,"category":"Default Category/Test-Category/Lifestyle/Lake"}],"stock_item":{"qty":"100","is_in_stock":true}},"custom_attributes":[{"attribute_code":"description","value":"This is new update The Champ Tee keeps you cool and dry while you do your thing. Let everyone know who you are by adding your name on the back for only $10."},{"attribute_code":"short_description","value":"The Champ Tee keeps you cool and dry while you do your thing. Let everyone know who you are by adding your name on the back for only $10."},{"attribute_code":"tax_class","value":"Refund Adjustments"},{"attribute_code":"price_level","value":"level 2"},{"attribute_code":"color_swatch","value":"Gray"},{"attribute_code":"color","value":"Blue"},{"attribute_code":"multiselect_labeltest","value":["Multi test I","Multi test II","Multi test III"]}]}}' erp.catalog.product.create.update

c) Base Cost Create Update
sudo php bin/magento queue:consumer:start baseCostCreateUpdateConsumer
sudo php bin/magento queue:message:publish '{"product":{"table":"base_cost","fields":{"base_cost_media": "22", "base_cost_treatment": "33","glass_size_short": "44","glass_size_long": "59","base_cost": "223222"}}}' erp.base.cost.create.update


d)Media Treatment Create Update 
sudo php bin/magento queue:consumer:start mediaTreatmentCreateUpdateConsumer
sudo php bin/magento queue:message:publish '{ "product": { "flat_tables": { "media": { "table": "media", "fields": { "sku": "", "base_cost_media": "", "display_name": "", "display_to_customer": "", "min_image_size_short": "", "min_image_size_long": "", "max_image_size_short": "", "max_image_size_long": "" } },"treatment": { "table": "treatment", "fields": { "treatment_sku": "", "base_cost_treatment": "", "display_name": "", "min_glass_size_short": "", "min_glass_size_long": "", "max_glass_size_short": "", "max_glass_size_long": "", "min_rabbet_depth": "", "requires_top_mat": "","requires_bottom_mat": "","requires_liner": "","image_edge_treatment": "","new_top_mat_size_left": "","new_top_mat_size_top": "","new_top_mat_size_right": "","new_top_mat_size_bottom": "","new_bottom_mat_size_left": "","new_bottom_mat_size_top": "","new_bottom_mat_size_right": "","new_bottom_mat_size_bottom": ""} },"media_treatment": { "table": "media_treatment", "fields": {"media_sku": "", "treatment_sku": "", "display_to_customer": ""} }, "frame_treatment": { "table": "frame_treatment", "fields": {"treatment_sku": "", "frame_type": ""} } } }}' erp.media.treatment.create.update

sudo php bin/magento queue:message:publish '{ "product": { "flat_tables": { "media": { "table": "media", "fields": { "sku": "eeee", "base_cost_media": "11", "display_name": "33", "display_to_customer": "vv", "min_image_size_short": "9", "min_image_size_long": "2", "max_image_size_short": "1", "max_image_size_long": "2" } },"treatment": { "table": "treatment", "fields": { "treatment_sku": "333", "base_cost_treatment": "2222", "display_name": "1", "min_glass_size_short": "1", "min_glass_size_long": "1", "max_glass_size_short": "1", "max_glass_size_long": "1", "min_rabbet_depth": "1", "requires_top_mat": "1","requires_bottom_mat": "1","requires_liner": "1","image_edge_treatment": "1","new_top_mat_size_left": "1","new_top_mat_size_top": "1","new_top_mat_size_right": "1","new_top_mat_size_bottom": "1","new_bottom_mat_size_left": "1","new_bottom_mat_size_top": "1","new_bottom_mat_size_right": "1","new_bottom_mat_size_bottom": "1"} },"media_treatment": { "table": "media_treatment", "fields": {"media_sku": "qq", "treatment_sku": "sss", "display_to_customer": "0"} }, "frame_treatment": { "table": "frame_treatment", "fields": {"treatment_sku": "21111","frame_type": "44"} } } }}' erp.media.treatment.create.update


e) Create/Update Order from SysPro to Magento:

 i) php bin/magento queue:consumers:start orderCreateUpdateConsumer
 
 ii) php bin/magento queue:message:publish '{"operation":"create","data":[{"web_order_id":"","syspro_order_id":"","syspro_customer_id":"","customer_email":"perficienttest-linda@newageinteriors.com","created_at":"2021-01-29 16:48:19","updated_at":"2021-01-29 16:48:19","quick_ship":"1","order_status":"pending","coupon_code":null,"discount_amount":55.35,"payment_method":"checkmo","customer_po_number":"PO-12345","shipping_method":"flatrate_flatrate","shipping_amount":10,"tax_amount":6.07,"subtotal":123,"grand_total":194.42,"lead_time":"Standard lead time is currently three weeks.","order_sidemark":"This is an order comment.","syspro_salesrep_id":"111","web_company_id":"The Final Touch","syspro_order_entry_date":"2021-01-19","requested_delivery_date":"2021-01-19","customer_due_date":"2021-01-29","expected_ship_date":"2021-01-29","billing_address":{"company_name":"Billing Company","region_code":"NY","country_id":"US","street1":"94 E. Jefryn Blvd.","street2":"Unit E","postcode":"11729","city":"Deer Park"},"shipping_address":{"region_code":"NY","country_id":"US","street1":"94 E. Jefryn Blvd.","street2":"Unit E","postcode":"11729","city":"Deer Park","telephone":"631-392-0866","firstname":"carolina","lastname":"Gomez","location":"Office","delivery_appointment":"2","loading_dock_available":"2"},"items":[{"sku":"Testprodu1","name":"Horizontal Bouquet I","qty":1,"price":73,"discount_amount":-32.85,"tax_amount":0,"row_total":73,"avatax_nsavtx":"1","avatax_entusecodlin":"2","avatax_nstkwh":"3","avatax_mscavx":"4","cart_properties":[{"selected_option":"Liner","selected_value":"red"},{"selected_option":"Medium","selected_value":"paper"},{"selected_option":"Frame","selected_value":"M0121"},{"selected_option":"Top Mat","selected_value":"T132"},{"selected_option":"Bottom Mat","selected_value":"B132"},{"selected_option":"Size","selected_value":"100\"w x 100\"h"},{"selected_option":"Side Mark","selected_value":"Side..."}],"configuration_options":{"medium":"paper","treatment":"","frame_sku":"","liner_sku":"red","top_mat_sku":"","bottom_mat_sku":"","top_mat_size_bottom":null,"top_mat_size_left":null,"top_mat_size_right":null,"top_mat_size_top":null,"bottom_mat_size_bottom":null,"bottom_mat_size_left":null,"bottom_mat_size_right":null,"bottom_mat_size_top":null,"image_width":"16","image_height":"16","glass_width":"16","glass_height":"16","item_width":16,"item_height":16}},{"sku":"surcharge-sku","name":"Surcharge Product","qty":1,"price":50,"discount_amount":-22.5,"tax_amount":6.07,"row_total":50,"cart_properties":[],"configuration_options":{"medium":"","treatment":"","frame_sku":"","liner_sku":"","top_mat_sku":"","bottom_mat_sku":"","top_mat_size_bottom":null,"top_mat_size_left":null,"top_mat_size_right":null,"top_mat_size_top":null,"bottom_mat_size_bottom":null,"bottom_mat_size_left":null,"bottom_mat_size_right":null,"bottom_mat_size_top":null,"image_width":"16","image_height":"16","glass_width":"16","glass_height":"16","item_width":16,"item_height":16}}],"shipments":[],"payment":{"method":"checkmo","transaction_id":null,"additional_information":{"method_title":"Pay on Terms"},"tokenbase_id":null,"amount_ordered":0,"amount_paid":0}}]}' erp.order.create.update