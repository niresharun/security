<?php
/**
 * Product Image Mapping
 * @category: Magento
 * @package: Perficient/CatalogPermissions
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Divya Sree<divya.sree@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for CRM Admin Mapping
 */
class CrmAdminMapping implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */

    const PRODUCT_PATH = 'perficient_crmconnector/product/mapping';
    const CUSTOMER_PATH = 'perficient_crmconnector/customer/mapping';
    const CUSTOMERADDRESS_PATH = 'perficient_crmconnector/customer_address/mapping';
    const ORDERMAPPING_PATH = 'perficient_crmconnector/order/mapping';
    const ORDERITEMS_PATH = 'perficient_crmconnector/order/items';
    const ORDERADDRESS_PATH = 'perficient_crmconnector/order/address';
    const ORDERCOMMENTS_PATH = 'perficient_crmconnector/order/comments';
    const CATEGORYMAPPING_PATH = 'perficient_crmconnector/category/mapping';
    const WISHLISTMAPPING_PATH = 'perficient_crmconnector/wishlist/mapping';
    const MYCATALOGTEMPLATES_PATH = 'perficient_crmconnector/mycatalog/mapping_templates';
    const MYCATALOGCATALOG_PATH = 'perficient_crmconnector/mycatalog/mapping_catalogs';
    const MYCATALOGPAGE_PATH = 'perficient_crmconnector/mycatalog/mapping_page';
    const PRODUCT_VALUE = '{"_1597756904569_569":{"regexp":"entity_id","value":"itemId","child":""},"_1597757007591_591":{"regexp":"sku","value":"sku","child":""},"_1597757022895_895":{"regexp":"name","value":"name","child":""},"_1597757027599_599":{"regexp":"type_id","value":"type","child":""},"_1597757036391_391":{"regexp":"status","value":"status","child":""},"_1597757052343_343":{"regexp":"visibility","value":"visibility","child":""},"_1597757061863_863":{"regexp":"art_configuration_type","value":"configurationType","child":""},"_1597757083751_751":{"regexp":"price_level","value":"priceLevel","child":""},"_1597757183464_464":{"regexp":"frame_skus","value":"availableFrames","child":""},"_1597757199447_447":{"regexp":"top_mat_skus","value":"availableTopMats","child":""},"_1597757224824_824":{"regexp":"bottom_mat_skus","value":"availableBottomMats","child":""},"_1597757242751_751":{"regexp":"top_mat_width","value":"topMatWidth","child":""},"_1597757257439_439":{"regexp":"bottom_mat_width","value":"bottomMatWidth","child":""},"_1597757275359_359":{"regexp":"color","value":"color","child":""},"_1597757290215_215":{"regexp":"frame_depth","value":"frameDepth","child":""},"_1597757302991_991":{"regexp":"frame_max_size","value":"frameMaxSize","child":""},"_1597757318015_15":{"regexp":"specialty","value":"specialty","child":""},"_1597757342639_639":{"regexp":"liner_width","value":"liner_width","child":""},"_1597757347750_750":{"regexp":"liner_sku","value":"linerSku","child":""},"_1597757403407_407":{"regexp":"mat_padding","value":"matPadding","child":"top,right,bottom,left"},"_1597757619414_414":{"regexp":"cropped","value":"croppedImage","child":""},"_1597757640038_38":{"regexp":"corner1_img","value":"frameImageCorner1","child":""},"_1597757649086_86":{"regexp":"corner2_img","value":"frameImageCorner2","child":""},"_1597757663495_495":{"regexp":"length1_img","value":"frameImageLength1","child":""},"_1597757678655_655":{"regexp":"length2_img","value":"frameImageLength2","child":""},"_1597757709816_816":{"regexp":"new_number","value":"numberOrder","child":""},"_1597757724230_230":{"regexp":"item_height","value":"height","child":""},"_1597757742190_190":{"regexp":"item_width","value":"width","child":""},"_1597757751046_46":{"regexp":"category_ids","value":"categoryIds","child":""},"_1597757765334_334":{"regexp":"related_products","value":"relatedProducts","child":""},"_1597757779014_14":{"regexp":"uuid","value":"UUID","child":""},"_1597757796391_391":{"regexp":"attributeSetName","value":"attributeSetName","child":""},"_1597757805222_222":{"regexp":"product_customizer","value":"isArtConfigurable","child":""},"_1597757865262_262":{"regexp":"mat_pattern","value":"matPattern","child":""},"_1597757896368_368":{"regexp":"image","value":"image","child":""},"_1597757906239_239":{"regexp":"small_image","value":"smallImage","child":""},"_1597757919438_438":{"regexp":"thumbnail","value":"imageThumbnail","child":""},"_1597757932998_998":{"regexp":"frame_default_sku","value":"frameDefaultSku","child":""},"_1597757945870_870":{"regexp":"top_mat_default_sku","value":"topMatDefaultSku","child":""},"_1597757963286_286":{"regexp":"bottom_mat_default_sku","value":"bottomMatDefaultSku","child":""},"_1597757987295_295":{"regexp":"price","value":"defaultPrice","child":""},"_1597758001439_439":{"regexp":"package_uuid","value":"packageUUID","child":""},"_1597758010022_22":{"regexp":"frame_width","value":"frameWidth","child":""},"_1597758056446_446":{"regexp":"color_frame","value":"frameColor","child":""},"_1597758071302_302":{"regexp":"visible","value":"visible","child":""},"_1597758079694_694":{"regexp":"count","value":"count","child":""},"_1597758093574_574":{"regexp":"data","value":"data","child":""},"_1597758107807_807":{"regexp":"metadata","value":"metadata","child":""},"_1597758120583_583":{"regexp":"medium","value":"medium","child":""},"_1597758154503_503":{"regexp":"tradeshow_added","value":"tradeshowAdded","child":""},"_1597758165071_71":{"regexp":"year_added","value":"yearAdded","child":""},"_1597758178942_942":{"regexp":"updated_at","value":"updated","child":""},"_1597758196806_806":{"regexp":"created_at","value":"created","child":""},"_1611154357016_16":{"regexp":"single_corner","value":"single_corner","child":""},"_1611154357016_13362":{"regexp":"artist_name","value":"artist_name","child":""},"_1611154357016_13361":{"regexp":"art_mount_type","value":"art_mount_type","child":""},"_1611154357016_13360":{"regexp":"bottom_mat_size_bottom","value":"bottom_mat_size_bottom","child":""},"_1611154357016_13359":{"regexp":"bottom_mat_size_left","value":"bottom_mat_size_left","child":""},"_1611154357016_13358":{"regexp":"bottom_mat_size_right","value":"bottom_mat_size_right","child":""},"_1611154357016_13357":{"regexp":"bottom_mat_size_top","value":"bottom_mat_size_top","child":""},"_1611154357016_13356":{"regexp":"category_list","value":"category_list","child":""},"_1611154357016_13355":{"regexp":"specialty_note","value":"specialty_note","child":""},"_1611154357016_13354":{"regexp":"syspro_number","value":"syspro_number","child":""},"_1611154357016_13353":{"regexp":"test","value":"test","child":""},"_1611154357016_13352":{"regexp":"top_mat_size_bottom","value":"top_mat_size_bottom","child":""},"_1611154357016_13351":{"regexp":"top_mat_size_left","value":"top_mat_size_left","child":""},"_1611154357016_13350":{"regexp":"top_mat_size_right","value":"top_mat_size_right","child":""},"_1611154357016_13345":{"regexp":"top_mat_size_top","value":"top_mat_size_top","child":""},"_1611154357016_13344":{"regexp":"treatment","value":"treatment","child":""},"_1611154357016_13343":{"regexp":"wag_number","value":"wag_number","child":""},"_1611154357016_13342":{"regexp":"color_family","value":"color_family","child":""},"_1611154357016_13341":{"regexp":"color_mat","value":"color_mat","child":""},"_1611154357016_13340":{"regexp":"color_swatch","value":"color_swatch","child":""},"_1611154357016_13339":{"regexp":"configuration_level","value":"configuration_level","child":""},"_1611154357016_13338":{"regexp":"cost","value":"cost","child":""},"_1611154357016_13337":{"regexp":"default_configurations","value":"default_configurations","child":""},"_1611154357016_13336":{"regexp":"default_item_price","value":"default_item_price","child":""},"_1611154357016_13335":{"regexp":"fabric_cost_per_lin_ft","value":"fabric_cost_per_lin_ft","child":""},"_1611154357016_13334":{"regexp":"filter_thickness","value":"filter_thickness","child":""},"_1611154357016_13333":{"regexp":"filter_type","value":"filter_type","child":""},"_1611154357016_13331":{"regexp":"frame_family","value":"frame_family","child":""},"_1611154357016_13330":{"regexp":"frame_material","value":"frame_material","child":""},"_1611154357016_13329":{"regexp":"frame_rabbet_depth","value":"frame_rabbet_depth","child":""},"_1611154357016_13328":{"regexp":"frame_type","value":"frame_type","child":""},"_1611154357016_13327":{"regexp":"frame_width","value":"frame_width","child":""},"_1611154357016_13324":{"regexp":"image_height","value":"image_height","child":""},"_1611154357016_13323":{"regexp":"image_width","value":"image_width","child":""},"_1611154357016_13320":{"regexp":"keyword_list","value":"keyword_list","child":""},"_1611154357016_13319":{"regexp":"landed_cost_per_foot","value":"landed_cost_per_foot","child":""},"_1611154357016_13318":{"regexp":"licensed_collection","value":"licensed_collection","child":""},"_1611154357016_13316":{"regexp":"lifestyle_image","value":"lifestyle_image","child":""},"_1611154357016_13315":{"regexp":"manufacturer","value":"manufacturer","child":""},"_1611154357016_13313":{"regexp":"mat_type","value":"mat_type","child":""},"_1611154357016_13312":{"regexp":"max_image_height","value":"max_image_height","child":""},"_1611154357016_13311":{"regexp":"max_image_width","value":"max_image_width","child":""},"_1611154357016_1339":{"regexp":"max_outer_size","value":"max_outer_size","child":""},"_1611154357016_1338":{"regexp":"media_category","value":"media_category","child":""},"_1611154357016_1337":{"regexp":"mirror_bevel","value":"mirror_bevel","child":""},"_1611154357016_1336":{"regexp":"moulding_waste_pct","value":"moulding_waste_pct","child":""},"_1611154357016_1335":{"regexp":"orientation","value":"orientation","child":""},"_1611154357016_1334":{"regexp":"other_skus_in_series","value":"other_skus_in_series","child":""},"_1611154357016_1333":{"regexp":"related_items","value":"related_items","child":""},"_1611154357016_1332":{"regexp":"simplified_medium","value":"simplified_medium","child":""},"_1611154357016_1331":{"regexp":"simplified_size","value":"simplified_size","child":""},"_1611226132344_344":{"regexp":"xxx","value":"zzzz","child":""},"_1611154357016_13363":{"regexp":"allow_message","value":"allow_message","child":""},"_1611154357016_13364":{"regexp":"allow_open_amount","value":"allow_open_amount","child":""},"_1611154357016_13365":{"regexp":"country_of_manufacture","value":"country_of_manufacture","child":""},"_1611154357016_13366":{"regexp":"cropper_image","value":"cropper_image","child":""},"_1611154357016_13367":{"regexp":"cross_domain_store","value":"cross_domain_store","child":""},"_1611154357016_13368":{"regexp":"cross_domain_url","value":"cross_domain_url","child":""},"_1611154357016_13369":{"regexp":"custom_design","value":"custom_design","child":""},"_1611154357016_13370":{"regexp":"custom_design_from","value":"custom_design_from","child":""},"_1611154357016_13371":{"regexp":"custom_design_to","value":"custom_design_to","child":""},"_1611154357016_13372":{"regexp":"custom_layout","value":"custom_layout","child":""},"_1611154357016_13373":{"regexp":"custom_layout_update","value":"custom_layout_update","child":""},"_1611154357016_13374":{"regexp":"custom_layout_update_file","value":"custom_layout_update_file","child":""},"_1611154357016_13375":{"regexp":"default_option","value":"default_option","child":""},"_1611154357016_13377":{"regexp":"double_corner","value":"double_corner","child":""},"_1611154357016_13378":{"regexp":"email_template","value":"email_template","child":""},"_1611154357016_13380":{"regexp":"gallery","value":"gallery","child":""},"_1611154357016_13381":{"regexp":"giftcard_amounts","value":"giftcard_amounts","child":""},"_1611154357016_13382":{"regexp":"giftcard_type","value":"giftcard_type","child":""},"_1611154357016_13385":{"regexp":"gift_wrapping_price","value":"gift_wrapping_price","child":""},"_1611154357016_13387":{"regexp":"image_label","value":"image_label","child":""},"_1611154357016_13390":{"regexp":"is_redeemable","value":"is_redeemable","child":""},"_1611154357016_13392":{"regexp":"lifetime","value":"lifetime","child":""},"_1611154357016_13393":{"regexp":"links_exist","value":"links_exist","child":""},"_1611154357016_13394":{"regexp":"links_purchased_separately","value":"links_purchased_separately","child":""},"_1611154357016_13395":{"regexp":"links_title","value":"links_title","child":""},"_1611154357016_13397":{"regexp":"meta_description","value":"meta_description","child":""},"_1611154357016_13398":{"regexp":"meta_keyword","value":"meta_keyword","child":""},"_1611154357016_13399":{"regexp":"meta_robots","value":"meta_robots","child":""},"_1611154357016_1339100":{"regexp":"meta_title","value":"meta_title","child":""},"_1611154357016_1339101":{"regexp":"minimal_price","value":"minimal_price","child":""},"_1611154357016_1339102":{"regexp":"msrp","value":"msrp","child":""},"_1611154357016_1339104":{"regexp":"news_from_date","value":"news_from_date","child":""},"_1611154357016_1339105":{"regexp":"news_to_date","value":"news_to_date","child":""},"_1611154357016_1339106":{"regexp":"old_id","value":"old_id","child":""},"_1611154357016_1339107":{"regexp":"open_amount_max","value":"open_amount_max","child":""},"_1611154357016_1339108":{"regexp":"open_amount_min","value":"open_amount_min","child":""},"_1611154357016_1339110":{"regexp":"page_layout","value":"page_layout","child":""},"_1611154357016_1339112":{"regexp":"price_type","value":"price_type","child":""},"_1611154357016_1339113":{"regexp":"price_view","value":"price_view","child":""},"_1611154357016_1339115":{"regexp":"product_seo_name","value":"product_seo_name","child":""},"_1611154357016_1339117":{"regexp":"related_tgtr_position_behavior","value":"related_tgtr_position_behavior","child":""},"_1611154357016_1339118":{"regexp":"related_tgtr_position_limit","value":"related_tgtr_position_limit","child":""},"_1611154357016_1339119":{"regexp":"renderer_corner","value":"renderer_corner","child":""},"_1611154357016_1339120":{"regexp":"renderer_length","value":"renderer_length","child":""},"_1611154357016_1339122":{"regexp":"samples_title","value":"samples_title","child":""},"_1611154357016_1339123":{"regexp":"shipment_type","value":"shipment_type","child":""},"_1611154357016_1339125":{"regexp":"sku_type","value":"sku_type","child":""},"_1611154357016_1339126":{"regexp":"small_image_label","value":"small_image_label","child":""},"_1611154357016_1339127":{"regexp":"special_from_date","value":"special_from_date","child":""},"_1611154357016_1339128":{"regexp":"special_price","value":"special_price","child":""},"_1611154357016_1339129":{"regexp":"special_to_date","value":"special_to_date","child":""},"_1611154357016_1339130":{"regexp":"spec_details","value":"spec_details","child":""},"_1611154357016_1339131":{"regexp":"swatch_image","value":"swatch_image","child":""},"_1611154357016_1339133":{"regexp":"thumbnail_label","value":"thumbnail_label","child":""},"_1611154357016_1339134":{"regexp":"tier_price","value":"tier_price","child":""},"_1611154357016_1339136":{"regexp":"upsell_tgtr_position_behavior","value":"upsell_tgtr_position_behavior","child":""},"_1611154357016_1339137":{"regexp":"upsell_tgtr_position_limit","value":"upsell_tgtr_position_limit","child":""},"_1611154357016_1339139":{"regexp":"url_path","value":"url_path","child":""},"_1611154357016_1339140":{"regexp":"use_config_allow_message","value":"use_config_allow_message","child":""},"_1611154357016_1339141":{"regexp":"use_config_email_template","value":"use_config_email_template","child":""},"_1611154357016_1339142":{"regexp":"use_config_is_redeemable","value":"use_config_is_redeemable","child":""},"_1611154357016_1339143":{"regexp":"use_config_lifetime","value":"use_config_lifetime","child":""},"_1611154357016_1339146":{"regexp":"weight_type","value":"weight_type","child":""},"_1617944686244_244":{"regexp":"filter_size","value":"size","child":""}}';
    const CUSTOMER_VALUE = '{"_1403608965781_781":{"regexp":"group_id","value":"customerGroup","child":""},"_1403608977556_556":{"regexp":"price_multiplier","value":"priceMultiplier","child":""},"_1403608984372_372":{"regexp":"created_at","value":"created","child":""},"_1403608994084_84":{"regexp":"updated_at","value":"updated","child":""},"_1403609003460_460":{"regexp":"customer_activated","value":"customerActivated","child":""},"_1403609012580_580":{"regexp":"prefix","value":"prefix","child":""},"_1403609018636_636":{"regexp":"firstname","value":"firstName","child":""},"_1403609030947_947":{"regexp":"middlename","value":"middleNameOrInitial","child":""},"_1403609038555_555":{"regexp":"lastname","value":"lastName","child":""},"_1403609048468_468":{"regexp":"suffix","value":"suffix","child":""},"_1403609061171_171":{"regexp":"email","value":"email","child":""},"_1403609074414_414":{"regexp":"telephone","value":"telephone","child":""},"_1403609079938_938":{"regexp":"fax","value":"fax","child":""},"_1403609086858_858":{"regexp":"default_billing","value":"defaultBillingAddress","child":""},"_1403609093870_870":{"regexp":"default_shipping","value":"defaultShippingAddress","child":""},"_1403770934746_746":{"regexp":"entity_id","value":"webCustomerNumber","child":""},"_1403771780784_784":{"regexp":"designer_type","value":"type","child":""},"_1403785424492_492":{"regexp":"addresses","value":"addresses","child":""},"_1404219169888_888":{"regexp":"website_id","value":"websiteId","child":""},"_1404219212286_286":{"regexp":"business_info","value":"businessInfo","child":""},"_1404219226878_878":{"regexp":"company","value":"company","child":""},"_1404219235095_95":{"regexp":"taxvat","value":"taxId","child":""},"_1407474718305_305":{"regexp":"uuid","value":"UUID","child":""},"_1407474719289_289":{"regexp":"is_vip","value":"isVIP","child":""},"_1407474719873_873":{"regexp":"is_customer_of","value":"isCustomerOf","child":""},"_1620774562951_951":{"regexp":"no_of_stores","value":"numberOfStores","child":""},"_1620774578648_648":{"regexp":"sq_ft_per_store","value":"sqFeetPerStore","child":""},"_1620774588011_11":{"regexp":"designer_type","value":"clientTypes","child":""},"_1620774606334_334":{"regexp":"no_of_jobs_per_year","value":"jobsPerYear","child":""},"_1620774747063_63":{"regexp":"no_of_designers","value":"numberOfDesigners","child":""},"_1620774768871_871":{"regexp":"percent_of_design","value":"percentageInDesign","child":""},"_1620774809032_32":{"regexp":"mark_pos","value":"marketingPosition","child":""}}';
    const CUSTOMERADDRESS_VALUE = '{"_1403782745145_145":{"regexp":"lastname","value":"lastName","child":""},"_1403782746233_233":{"regexp":"firstname","value":"firstName","child":""},"_1403782874726_726":{"regexp":"middlename","value":"middleNameOrInitial","child":""},"_1403782875454_454":{"regexp":"prefix","value":"prefix","child":""},"_1403782876265_265":{"regexp":"suffix","value":"suffix","child":""},"_1403782877022_22":{"regexp":"company","value":"company","child":""},"_1403782877878_878":{"regexp":"street","value":"street","child":""},"_1403782878598_598":{"regexp":"city","value":"city","child":""},"_1403782879390_390":{"regexp":"region","value":"region","child":""},"_1403782880134_134":{"regexp":"postcode","value":"postcode","child":""},"_1403782880870_870":{"regexp":"country_id","value":"countryCode","child":""},"_1403782881702_702":{"regexp":"telephone","value":"telephone","child":""},"_1619147315813_813":{"regexp":"uuid","value":"uuid","child":""}}';
    const ORDERMAPPING_VALUE = '{"_1403860497192_192":{"regexp":"entity_id","value":"orderId","child":"webOrderNumber"},"_1403861856706_706":{"regexp":"increment_id","value":"orderId","child":"webOrderId"},"_1403861999664_664":{"regexp":"customer_id","value":"account","child":"customerId"},"_1403862071062_62":{"regexp":"items","value":"lineItems","child":""},"_1403863092042_42":{"regexp":"state","value":"status","child":""},"_1403863099175_175":{"regexp":"status","value":"status","child":""},"_1403864586763_763":{"regexp":"created_at","value":"created","child":""},"_1403864588035_35":{"regexp":"updated_at","value":"updated","child":""},"_1403864589675_675":{"regexp":"created_at","value":"becameOrder","child":""},"_1403865031213_213":{"regexp":"billing_address","value":"billingAddress","child":""},"_1403865126744_744":{"regexp":"shipping","value":"shipping","child":""},"_1403869939824_824":{"regexp":"base_currency_code","value":"currency","child":""},"_1403872011445_445":{"regexp":"payment","value":"payment","child":""},"_1403872476116_116":{"regexp":"_payment_method","value":"Allowpayment","child":""},"_1404804663036_36":{"regexp":"shipping_method","value":"shippingMethod","child":""},"_1404804947493_493":{"regexp":"store_id","value":"storeId","child":""},"_1404805135085_85":{"regexp":"mode","value":"mode","child":""},"_1404810941375_375":{"regexp":"addresses","value":"addresses","child":""},"_1404811025007_7":{"regexp":"customer","value":"customer","child":""},"_1404817845276_276":{"regexp":"shipping_instructions","value":"shippingInstructions","child":""},"_1404818423420_420":{"regexp":"loading_dock","value":"loadingDock","child":""},"_1404818445804_804":{"regexp":"lift_gate","value":"liftGateRequired","child":""},"_1404818492302_302":{"regexp":"semi_truck_available","value":"largeSemiCapable","child":""},"_1404818513949_949":{"regexp":"driver_notification_required","value":"carrierDriverNotification","child":""},"_1404818625190_190":{"regexp":"contact","value":"contactInfo","child":""},"_1407474835430_430":{"regexp":"order_source_event","value":"tradeshow","child":"UUID"},"_1407474836126_126":{"regexp":"order_source_rep","value":"account","child":"UUID"},"_1407474836790_790":{"regexp":"uuid","value":"UUID","child":""}}';
    const ORDERITEMS_VALUE = '{"_1403860498040_40":{"regexp":"item_id","value":"itemId","child":""},"_1403862256930_930":{"regexp":"product_type","value":"type","child":""},"_1403862299246_246":{"regexp":"price","value":"price","child":"amount"},"_1403862765112_112":{"regexp":"sku","value":"data","child":"itemCode"},"_1403862765861_861":{"regexp":"name","value":"data","child":"title"},"_1403862814804_804":{"regexp":"created_at","value":"created","child":""},"_1403862817540_540":{"regexp":"updated_at","value":"updated","child":""},"_1403862905526_526":{"regexp":"qty_ordered","value":"quantity","child":""},"_1403864367240_240":{"regexp":"itemcomment","value":"note","child":""},"_1404984334158_158":{"regexp":"package","value":"package","child":""}}';
    const ORDERADDRESS_VALUE = '{"_1403782745145_146":{"regexp":"lastname","value":"lastName","child":""},"_1403866007935_935":{"regexp":"firstname","value":"firstName","child":""},"_1403866008751_751":{"regexp":"middlename","value":"middleNameOrInitial","child":""},"_1403866009463_463":{"regexp":"prefix","value":"prefix","child":""},"_1403866010183_183":{"regexp":"suffix","value":"suffix","child":""},"_1403866010903_903":{"regexp":"company","value":"company","child":""},"_1403866011927_927":{"regexp":"street","value":"street","child":""},"_1403866184959_959":{"regexp":"city","value":"city","child":""},"_1403866188468_468":{"regexp":"region","value":"region","child":""},"_1403866189699_699":{"regexp":"postcode","value":"postcode","child":""},"_1403866191515_515":{"regexp":"country_id","value":"countryCode","child":""},"_1403866229166_166":{"regexp":"telephone","value":"telephone","child":""},"_1404805081381_381":{"regexp":"is_default_shipping","value":"isDefaultShipping","child":""},"_1404805102133_133":{"regexp":"is_default_billing","value":"isDefaultBilling","child":""}}';
    const ORDERCOMMENTS_VALUE = '{"_1403865327101_101":{"regexp":"created_at","value":"becameOrder","child":""},"_1403865330212_212":{"regexp":"comment","value":"note","child":""},"_1403865342847_847":{"regexp":"is_customer_notified","value":"is_customer_notified","child":""},"_1403865351396_396":{"regexp":"is_visible_on_front","value":"is_visible_on_front","child":""},"_1403865355348_348":{"regexp":"status","value":"status","child":""}}';
    const CATEGORYMAPPING_VALUE = '{"_1597756543850_850":{"regexp":"entity_id","value":"entity_id","child":""},"_1597756767696_696":{"regexp":"name","value":"name","child":""},"_1597756771760_760":{"regexp":"parent_id","value":"parentId","child":""},"_1597756798799_799":{"regexp":"image","value":"image","child":""},"_1597756811247_247":{"regexp":"is_active","value":"visible","child":""},"_1597756830928_928":{"regexp":"position","value":"position","child":""},"_1597756841168_168":{"regexp":"include_in_menu","value":"availableInTopNav","child":""},"_1597756858344_344":{"regexp":"uuid","value":"UUID","child":""},"_1597756871759_759":{"regexp":"created_at","value":"created","child":""},"_1597756886696_696":{"regexp":"updated_at","value":"updated","child":""},"_1597756886696_697":{"regexp":"entity_type_id","value":"entity_type_id","child":""},"_1597756886696_698":{"regexp":"attribute_set_id","value":"attribute_set_id","child":""},"_1597756886696_699":{"regexp":"path","value":"path","child":""},"_1597756886696_700":{"regexp":"level","value":"level","child":""},"_1597756886696_701":{"regexp":"children_count","value":"children_count","child":""}}';
    const WISHLISTMAPPING_VALUE = '{"_1410786628065_65":{"regexp":"wishlist_id","value":"magWishlistId","child":""},"_1410786638857_857":{"regexp":"customer_id","value":"magCustomerId","child":""},"_1410786647344_344":{"regexp":"shared","value":"shared","child":""},"_1410786654020_20":{"regexp":"sharing_code","value":"sharingCode","child":""},"_1410786662008_8":{"regexp":"updated_at","value":"updated","child":""},"_1410786666632_632":{"regexp":"name","value":"name","child":""},"_1410786671664_664":{"regexp":"visibility","value":"visibility","child":""},"_1410786679024_24":{"regexp":"wishlist_item_id","value":"magWishlistItemId","child":""},"_1410786686143_143":{"regexp":"description","value":"description","child":""},"_1410786695367_367":{"regexp":"added_at","value":"updated","child":""},"_1410786695943_943":{"regexp":"qty","value":"quantity","child":""},"_1410786696447_447":{"regexp":"code","value":"code","child":""},"_1410786697239_239":{"regexp":"value","value":"value","child":""},"_1410842753254_254":{"regexp":"product_id","value":"magProductId","child":""},"_1410861138140_140":{"regexp":"items","value":"items","child":""}}';
    const MYCATALOGTEMPLATES_VALUE = '{"_1410786628065_65":{"regexp":"wishlist_id","value":"magWishlistId","child":""},"_1410786638857_857":{"regexp":"customer_id","value":"magCustomerId","child":""},"_1410786647344_344":{"regexp":"shared","value":"shared","child":""},"_1410786654020_20":{"regexp":"sharing_code","value":"sharingCode","child":""},"_1410786662008_8":{"regexp":"updated_at","value":"updated","child":""},"_1410786666632_632":{"regexp":"name","value":"name","child":""},"_1410786671664_664":{"regexp":"visibility","value":"visibility","child":""},"_1410786679024_24":{"regexp":"wishlist_item_id","value":"magWishlistItemId","child":""},"_1410786686143_143":{"regexp":"description","value":"description","child":""},"_1410786695367_367":{"regexp":"added_at","value":"updated","child":""},"_1410786695943_943":{"regexp":"qty","value":"quantity","child":""},"_1410786696447_447":{"regexp":"code","value":"code","child":""},"_1410786697239_239":{"regexp":"value","value":"value","child":""},"_1410842753254_254":{"regexp":"product_id","value":"magProductId","child":""},"_1410861138140_140":{"regexp":"items","value":"items","child":""}}';
    const MYCATALOGCATALOG_VALUE = '{"_1411378830267_267":{"regexp":"catalog_id","value":"magCatalogId","child":""},"_1411378835427_427":{"regexp":"catalog_uuid","value":"catalogUUID","child":""},"_1411378840050_50":{"regexp":"customer_id","value":"magCustomerId","child":""},"_1411378844418_418":{"regexp":"wishlist_id","value":"magWishlistId","child":""},"_1411378848706_706":{"regexp":"logo_image","value":"logoImage","child":""},"_1411378853225_225":{"regexp":"catalog_title","value":"catalogTitle","child":""},"_1411378857540_540":{"regexp":"additional_info_1","value":"additionalInfo1","child":""},"_1411378861372_372":{"regexp":"additional_info_2","value":"additionalInfo2","child":""},"_1411378866513_513":{"regexp":"name","value":"presenterName","child":""},"_1411378871196_196":{"regexp":"company_name","value":"presenterCompanyName","child":""},"_1411378877480_480":{"regexp":"phone_number","value":"phoneNumber","child":""},"_1411378881560_560":{"regexp":"website_url","value":"websiteUrl","child":""},"_1411378890544_544":{"regexp":"created_date","value":"created_date","child":""},"_1411378895280_280":{"regexp":"updated_date","value":"updated_date","child":""},"_1411378899768_768":{"regexp":"price_on","value":"price_on","child":""},"_1411378903671_671":{"regexp":"price_modifier","value":"price_modifier","child":""}}';
    const MYCATALOGPAGE_VALUE = '{"_1411387617915_915":{"regexp":"page_id","value":"magPageId","child":""},"_1411387625642_642":{"regexp":"page_uuid","value":"pageUUID","child":""},"_1411387639521_521":{"regexp":"catalog_id","value":"magCatalogId","child":""},"_1411387643985_985":{"regexp":"catalog_uuid","value":"catalogUUID","child":""},"_1411387648433_433":{"regexp":"page_template_id","value":"pageTemplateId","child":""},"_1411387653609_609":{"regexp":"drop_spot_config","value":"dropSpotConfig","child":""},"_1411387658057_57":{"regexp":"page_position","value":"pagePosition","child":""},"_1411387662369_369":{"regexp":"created_date","value":"created_date","child":""},"_1411387666561_561":{"regexp":"updated_date","value":"updated_date","child":""},"_1617837387162_162":{"regexp":"created_at","value":"created","child":""},"_1617837395563_563":{"regexp":"updated_at","value":"updated","child":""},"_1617837575804_804":{"regexp":"customer_id","value":"magCustomerId","child":""},"_1617837577622_622":{"regexp":"wishlist_id","value":"magWishlistId","child":""}}';
    const SCOPE_ID = false;

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected WriterInterface          $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected ScopeConfigInterface     $scopeConfig
    )
    {
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setProductMapping();
        $this->setCustomerMapping();
        $this->setCustomerAddressMapping();
        $this->setOrderMapping();
        $this->setOrderItems();
        $this->setOrderAddress();
        $this->setOrderComments();
        $this->setCategoryMapping();
        $this->setWishlistMapping();
        $this->setMyCatalogTemplates();
        $this->setMyCatalogCatalog();
        $this->setMyCatalogPage();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function setCustomerMapping()
    {
        $this->configWriter->save(self::CUSTOMER_PATH, self::CUSTOMER_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setCustomerAddressMapping()
    {
        $this->configWriter->save(self::CUSTOMERADDRESS_PATH, self::CUSTOMERADDRESS_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setOrderMapping()
    {
        $this->configWriter->save(self::ORDERMAPPING_PATH, self::ORDERMAPPING_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setOrderItems()
    {
        $this->configWriter->save(self::ORDERITEMS_PATH, self::ORDERITEMS_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setOrderAddress()
    {
        $this->configWriter->save(self::ORDERADDRESS_PATH, self::ORDERADDRESS_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setOrderComments()
    {
        $this->configWriter->save(self::ORDERCOMMENTS_PATH, self::ORDERCOMMENTS_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setCategoryMapping()
    {
        $this->configWriter->save(self::CATEGORYMAPPING_PATH, self::CATEGORYMAPPING_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setWishlistMapping()
    {
        $this->configWriter->save(self::WISHLISTMAPPING_PATH, self::WISHLISTMAPPING_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setMyCatalogTemplates()
    {
        $this->configWriter->save(self::MYCATALOGTEMPLATES_PATH, self::MYCATALOGTEMPLATES_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setMyCatalogCatalog()
    {
        $this->configWriter->save(self::MYCATALOGCATALOG_PATH, self::MYCATALOGCATALOG_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function setMyCatalogPage()
    {
        $this->configWriter->save(self::MYCATALOGPAGE_PATH, self::MYCATALOGPAGE_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Set Product Mapping
     */
    public function setProductMapping()
    {
        $this->configWriter->save(self::PRODUCT_PATH, self::PRODUCT_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
