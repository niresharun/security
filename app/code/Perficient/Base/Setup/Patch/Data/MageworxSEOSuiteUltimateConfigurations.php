<?php
/**
 * This file is used to add Mageworx SEO Suite Ultimate Configurations
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Config\Model\Config\Backend\Encrypted;

/**
 * Class MageworxSEOSuiteUltimateConfigurations
 * @package Perficient\Base\Setup\Patch\Data
 */
class MageworxSEOSuiteUltimateConfigurations implements DataPatchInterface
{

   final const YES =  true;

   final const NO  =  false;

    /**#@+
     * Constants defined for xpath of system configuration
     */

    final const XML_SITEMAP_MIRASVIT_BLOG_ADD_BLOG_PAGES	= 'mageworx_seo/xml_sitemap/xml_sitemap_mirasvit_blog/add_blog_pages';

    final const XML_SITEMAP_MAGEPLAZA_BLOG_ADD_BLOG_PAGES	= 'mageworx_seo/xml_sitemap/xml_sitemap_mageplaza_blog/add_blog_pages';

    final const XML_SITEMAP_AHEADWORKS_BLOG_ADD_BLOG_PAGES	= 'mageworx_seo/xml_sitemap/xml_sitemap_aheadworks_blog/add_blog_pages';

    final const PRODUCT_VIDEO_INCLUDE	= 'mageworx_seo/xml_sitemap/product_video_include';

    final const PRODUCT_IMAGE_SOURCE	= 'mageworx_seo/xml_sitemap/product_image_source';

    final const META_ROBOTS_EXCLUSION	= 'mageworx_seo/xml_sitemap/meta_robots_exclusion';

    final const HOMEPAGE_OPTIMIZE	= 'mageworx_seo/xml_sitemap/homepage_optimize';

    final const EXCLUDE_OUT_OF_STOCK_PRODUCTS	= 'mageworx_seo/xml_sitemap/exclude_out_of_stock_products';

    final const ENABLE_VALIDATE_URLS	= 'mageworx_seo/xml_sitemap/enable_validate_urls';

    final const ENABLE_ADDITIONAL_LINKS	= 'mageworx_seo/xml_sitemap/enable_additional_links';

    final const CHECK_URLS_AVAILABILITY	= 'mageworx_seo/xml_sitemap/check_urls_availability';

    final const CATEGORY_IMAGE_INCLUDE	= 'mageworx_seo/xml_sitemap/category_image_include';

    final const ADDITIONAL_LINKS	= 'mageworx_seo/xml_sitemap/additional_links';

    final const CATEGORY_USE_INVERT_REDIRECT	= 'mageworx_seo/urls/category/use_invert_redirect';

    final const CATEGORY_USE_IN_PAGER	= 'mageworx_seo/urls/category/use_in_pager';

    final const CATEGORY_USE_IN_ATTRIBUTE	= 'mageworx_seo/urls/category/use_in_attribute';

    final const USE_PRODUCT_SEO_NAME	= 'mageworx_seo/seoxtemplates/use_product_seo_name';

    final const USE_CATEGORY_SEO_NAME	= 'mageworx_seo/seoxtemplates/use_category_seo_name';

    final const ENABLED_CRON_NOTIFY	= 'mageworx_seo/seoxtemplates/enabled_cron_notify';

    final const CROP_ROOT_CATEGORY	= 'mageworx_seo/seoxtemplates/crop_root_category';

    final const CROP_META_TITLE	= 'mageworx_seo/seoxtemplates/crop_meta_title';

    final const CROP_META_DESCRIPTION	= 'mageworx_seo/seoxtemplates/crop_meta_description';

    final const DELETED_PRODUCT_REDIRECT_TYPE	= 'mageworx_seo/seoredirects/deleted_product/redirect_type';

    final const DELETED_PRODUCT_REDIRECT_TARGET	= 'mageworx_seo/seoredirects/deleted_product/redirect_target';

    final const DELETED_PRODUCT_ENABLED	= 'mageworx_seo/seoredirects/deleted_product/enabled';

    final const DELETED_PRODUCT_COUNT_STABLE_DAY	= 'mageworx_seo/seoredirects/deleted_product/count_stable_day';

    final const CUSTOM_KEEP_FOR_DELETED_ENTITIES	= 'mageworx_seo/seoredirects/custom/keep_for_deleted_entities';

    final const CUSTOM_ENABLED	= 'mageworx_seo/seoredirects/custom/enabled';

    final const USE_NAME_FOR_TITLE	= 'mageworx_seo/seocrosslinks/use_name_for_title';

    final const REPLACEMENT_COUNT_PRODUCT	= 'mageworx_seo/seocrosslinks/replacement_count_product';

    final const REPLACEMENT_COUNT_CMS_PAGE	= 'mageworx_seo/seocrosslinks/replacement_count_cms_page';

    final const REPLACEMENT_COUNT_CATEGORY	= 'mageworx_seo/seocrosslinks/replacement_count_category';

    final const PRODUCT_ATTRIBUTES	= 'mageworx_seo/seocrosslinks/product_attributes';

    final const ENABLED	= 'mageworx_seo/seocrosslinks/enabled';

    final const DEFAULT_TARGET	= 'mageworx_seo/seocrosslinks/default_target';

    final const DEFAULT_STATUS	= 'mageworx_seo/seocrosslinks/default_status';

    final const DEFAULT_REPLACEMENT_COUNT	= 'mageworx_seo/seocrosslinks/default_replacement_count';

    final const DEFAULT_REFERENCE	= 'mageworx_seo/seocrosslinks/default_reference';

    final const DEFAULT_PRIORITY	= 'mageworx_seo/seocrosslinks/default_priority';

    final const DEFAULT_DESTINATION	= 'mageworx_seo/seocrosslinks/default_destination';

    final const WEBSITE_WEBSITE_USE_SEARCH	= 'mageworx_seo/markup/website/website_use_search';

    final const WEBSITE_TW_ENABLED	= 'mageworx_seo/markup/website/tw_enabled';

    final const WEBSITE_RS_ENABLED	= 'mageworx_seo/markup/website/rs_enabled';

    final const WEBSITE_OG_IMAGE	= 'mageworx_seo/markup/website/og_image';

    final const WEBSITE_OG_ENABLED	= 'mageworx_seo/markup/website/og_enabled';

    final const WEBSITE_NAME	= 'mageworx_seo/markup/website/name';

    final const WEBSITE_FB_APP_ID	= 'mageworx_seo/markup/website/fb_app_id';

    final const WEBSITE_DESCRIPTION	= 'mageworx_seo/markup/website/description';

    final const SELLER_TYPE	= 'mageworx_seo/markup/seller/type';

    final const SELLER_STREET	= 'mageworx_seo/markup/seller/street';

    final const SELLER_SHOW_ON_PAGES	= 'mageworx_seo/markup/seller/show_on_pages';

    final const SELLER_SAME_AS_LINKS	= 'mageworx_seo/markup/seller/same_as_links';

    final const SELLER_RS_ENABLED	= 'mageworx_seo/markup/seller/rs_enabled';

    final const SELLER_REGION	= 'mageworx_seo/markup/seller/region';

    final const SELLER_PRICE_RANGE	= 'mageworx_seo/markup/seller/price_range';

    final const SELLER_POST_CODE	= 'mageworx_seo/markup/seller/post_code';

    final const SELLER_PHONE	= 'mageworx_seo/markup/seller/phone';

    final const SELLER_NAME	= 'mageworx_seo/markup/seller/name';

    final const SELLER_LOCATION	= 'mageworx_seo/markup/seller/location';

    final const SELLER_IMAGE	= 'mageworx_seo/markup/seller/image';

    final const SELLER_FAX	= 'mageworx_seo/markup/seller/fax';

    final const SELLER_EMAIL	= 'mageworx_seo/markup/seller/email';

    final const SELLER_DESCRIPTION	= 'mageworx_seo/markup/seller/description';

    final const PRODUCT_WEIGHT_ENABLED	= 'mageworx_seo/markup/product/weight_enabled';

    final const PRODUCT_USE_MULTIPLE_OFFER	= 'mageworx_seo/markup/product/use_multiple_offer';

    final const PRODUCT_TW_ENABLED	= 'mageworx_seo/markup/product/tw_enabled';

    final const PRODUCT_SPECIAL_PRICE_FUNCTIONALITY	= 'mageworx_seo/markup/product/special_price_functionality';

    final const PRODUCT_SKU_ENABLED	= 'mageworx_seo/markup/product/sku_enabled';

    final const PRODUCT_RS_ENABLED_FOR_SPECIFIC_PRODUCT	= 'mageworx_seo/markup/product/rs_enabled_for_specific_product';

    final const PRODUCT_RS_ENABLED	= 'mageworx_seo/markup/product/rs_enabled';

    final const PRODUCT_PRODUCT_ID_CODE	= 'mageworx_seo/markup/product/product_id_code';

    final const PRODUCT_PRICE_VALID_UNTIL_DEFAULT_VALUE	= 'mageworx_seo/markup/product/price_valid_until_default_value';

    final const PRODUCT_OG_ENABLED	= 'mageworx_seo/markup/product/og_enabled';

    final const PRODUCT_MODEL_ENABLED	= 'mageworx_seo/markup/product/model_enabled';

    final const PRODUCT_MANUFACTURER_ENABLED	= 'mageworx_seo/markup/product/manufacturer_enabled';

    final const PRODUCT_GTIN_ENABLED	= 'mageworx_seo/markup/product/gtin_enabled';

    final const PRODUCT_GA_ENABLED	= 'mageworx_seo/markup/product/ga_enabled';

    final const PRODUCT_GA_CSS_SELECTOR	= 'mageworx_seo/markup/product/ga_css_selector';

    final const PRODUCT_DISABLE_DEFAULT_REVIEW	= 'mageworx_seo/markup/product/disable_default_review';

    final const PRODUCT_DESCRIPTION_CODE	= 'mageworx_seo/markup/product/description_code';

    final const PRODUCT_CUSTOM_PRORERTY_ENABLED	= 'mageworx_seo/markup/product/custom_prorerty_enabled';

    final const PRODUCT_CUSTOM_PRORERTIES	= 'mageworx_seo/markup/product/custom_prorerties';

    final const PRODUCT_CROP_HTML_IN_DESCRIPTION	= 'mageworx_seo/markup/product/crop_html_in_description';

    final const PRODUCT_CONDITION_ENABLED	= 'mageworx_seo/markup/product/condition_enabled';

    final const PRODUCT_COLOR_ENABLED	= 'mageworx_seo/markup/product/color_enabled';

    final const PRODUCT_CATEGORY_ENABLED	= 'mageworx_seo/markup/product/category_enabled';

    final const PRODUCT_BRAND_ENABLED	= 'mageworx_seo/markup/product/brand_enabled';

    final const PRODUCT_BEST_RATING	= 'mageworx_seo/markup/product/best_rating';

    final const PRODUCT_ADD_REVIEWS	= 'mageworx_seo/markup/product/add_reviews';

    final const PAGE_TW_ENABLED	= 'mageworx_seo/markup/page/tw_enabled';

    final const PAGE_OG_ENABLED	= 'mageworx_seo/markup/page/og_enabled';

    final const PAGE_GA_ENABLED	= 'mageworx_seo/markup/page/ga_enabled';

    final const PAGE_GA_CSS_SELECTOR	= 'mageworx_seo/markup/page/ga_css_selector';

    final const COMMON_TW_USERNAME	= 'mageworx_seo/markup/common/tw_username';

    final const CATEGORY_TW_ENABLED	= 'mageworx_seo/markup/category/tw_enabled';

    final const CATEGORY_RS_ENABLED	= 'mageworx_seo/markup/category/rs_enabled';

    final const CATEGORY_ROBOTS_RESTRICTION	= 'mageworx_seo/markup/category/robots_restriction';

    final const CATEGORY_OG_ENABLED	= 'mageworx_seo/markup/category/og_enabled';

    final const CATEGORY_GA_ENABLED	= 'mageworx_seo/markup/category/ga_enabled';

    final const CATEGORY_GA_CSS_SELECTOR	= 'mageworx_seo/markup/category/ga_css_selector';

    final const CATEGORY_ADD_PRODUCT_OFFERS	= 'mageworx_seo/markup/category/add_product_offers';

    final const BREADCRUMBS_RS_ENABLED	= 'mageworx_seo/markup/breadcrumbs/rs_enabled';

    final const USE_CAT_DISPLAY_MODE	= 'mageworx_seo/html_sitemap/use_cat_display_mode';

    final const TITLE	= 'mageworx_seo/html_sitemap/title';

    final const SHOW_STORES	= 'mageworx_seo/html_sitemap/show_stores';

    final const SHOW_PRODUCTS	= 'mageworx_seo/html_sitemap/show_products';

    final const SHOW_LINKS	= 'mageworx_seo/html_sitemap/show_links';

    final const SHOW_CUSTOM_LINKS	= 'mageworx_seo/html_sitemap/show_custom_links';

    final const SHOW_CMS_PAGES	= 'mageworx_seo/html_sitemap/show_cms_pages';

    final const SHOW_CATEGORIES	= 'mageworx_seo/html_sitemap/show_categories';

    final const PRODUCT_URL_LENGTH	= 'mageworx_seo/html_sitemap/product_url_length';

    final const META_KEYWORDS	= 'mageworx_seo/html_sitemap/meta_keywords';

    final const META_DESCRIPTION	= 'mageworx_seo/html_sitemap/meta_description';

    final const CATEGORY_MAX_DEPTH	= 'mageworx_seo/html_sitemap/category_max_depth';

    final const CAT_PROD_SORT_ORDER	= 'mageworx_seo/html_sitemap/cat_prod_sort_order';

    final const HTML_SITEMAP_ADDITIONAL_LINKS	= 'mageworx_seo/html_sitemap/additional_links ';

    final const SEO_FILTERS_USE_SEO_FOR_CATEGORY_FILTERS	= 'mageworx_seo/extended/seo_filters/use_seo_for_category_filters';

    final const SEO_FILTERS_USE_ON_SINGLE_FILTER	= 'mageworx_seo/extended/seo_filters/use_on_single_filter';

    final const META_PAGER_IN_TITLE	= 'mageworx_seo/extended/meta/pager_in_title';

    final const META_PAGER_IN_KEYWORDS	= 'mageworx_seo/extended/meta/pager_in_keywords';

    final const META_PAGER_IN_DESCRIPTION	= 'mageworx_seo/extended/meta/pager_in_description';

    final const META_LAYERED_FILTERS_IN_TITLE	= 'mageworx_seo/extended/meta/layered_filters_in_title';

    final const META_LAYERED_FILTERS_IN_KEYWORDS	= 'mageworx_seo/extended/meta/layered_filters_in_keywords';

    final const META_LAYERED_FILTERS_IN_DESCRIPTION	= 'mageworx_seo/extended/meta/layered_filters_in_description';

    final const META_CUT_TITLE_PREFIX_SUFFIX	= 'mageworx_seo/extended/meta/cut_title_prefix_suffix';

    final const META_CUT_PREFIX_SUFFIX_PAGES	= 'mageworx_seo/extended/meta/cut_prefix_suffix_pages';

    final const TRAILING_SLASH_HOME_PAGE	= 'mageworx_seo/common_sitemap/trailing_slash_home_page';

    final const TRAILING_SLASH	= 'mageworx_seo/common_sitemap/trailing_slash';

    final const BREADCRUMBS_TYPE	= 'mageworx_seo/breadcrumbs/type';

    final const BREADCRUMBS_ENABLED	= 'mageworx_seo/breadcrumbs/enabled';

    final const BREADCRUMBS_BY_CATEGORY_PRIORITY	= 'mageworx_seo/breadcrumbs/by_category_priority';

    final const BASE_USE_NEXT_PREV	= 'mageworx_seo/base/use_next_prev';

    final const BASE_ROBOTS_ROBOTS_FOR_LN_MULTIPLE	= 'mageworx_seo/base/robots/robots_for_ln_multiple';

    final const BASE_ROBOTS_NOINDEX_NOFOLLOW_USER_PAGES	= 'mageworx_seo/base/robots/noindex_nofollow_user_pages';

    final const BASE_ROBOTS_NOINDEX_FOLLOW_USER_PAGES	= 'mageworx_seo/base/robots/noindex_follow_user_pages';

    final const BASE_ROBOTS_NOINDEX_FOLLOW_PAGES	= 'mageworx_seo/base/robots/noindex_follow_pages';

    final const BASE_ROBOTS_COUNT_FILTERS_FOR_NOINDEX	= 'mageworx_seo/base/robots/count_filters_for_noindex';

    final const BASE_ROBOTS_CATEGORY_LN_PAGES_ROBOTS	= 'mageworx_seo/base/robots/category_ln_pages_robots';

    final const BASE_ROBOTS_ATTRIBUTE_SETTINGS	= 'mageworx_seo/base/robots/attribute_settings';

    final const BASE_HREFLANGS_X_DEFAULT_WEBSITE	= 'mageworx_seo/base/hreflangs/x_default_website';

    final const BASE_HREFLANGS_SCOPE	= 'mageworx_seo/base/hreflangs/scope';

    final const BASE_HREFLANGS_ENABLED	= 'mageworx_seo/base/hreflangs/enabled';

    final const BASE_HREFLANGS_CMS_RELATION_WAY	= 'mageworx_seo/base/hreflangs/cms_relation_way';

    final const BASE_CANONICAL_USE_PAGER_IN_CANONICAL	= 'mageworx_seo/base/canonical/use_pager_in_canonical';

    final const BASE_CANONICAL_USE_CANONICAL	= 'mageworx_seo/base/canonical/use_canonical';

    final const BASE_CANONICAL_TRAILING_SLASH_HOME_PAGE	= 'mageworx_seo/base/canonical/trailing_slash_home_page';

    final const BASE_CANONICAL_TRAILING_SLASH	= 'mageworx_seo/base/canonical/trailing_slash';

    final const BASE_CANONICAL_PRODUCT_CANONICAL_URL_TYPE	= 'mageworx_seo/base/canonical/product_canonical_url_type';

    final const BASE_CANONICAL_DISABLE_BY_ROBOTS	= 'mageworx_seo/base/canonical/disable_by_robots';

    final const BASE_CANONICAL_CROSS_DOMAIN_URL	= 'mageworx_seo/base/canonical/cross_domain_url';

    final const BASE_CANONICAL_CROSS_DOMAIN_STORE	= 'mageworx_seo/base/canonical/cross_domain_store';

    final const BASE_CANONICAL_CANONICAL_IGNORE_PAGES	= 'mageworx_seo/base/canonical/canonical_ignore_pages';

    final const BASE_CANONICAL_CANONICAL_FOR_LN_MULTIPLE	= 'mageworx_seo/base/canonical/canonical_for_ln_multiple';

    final const BASE_CANONICAL_CANONICAL_FOR_LN	= 'mageworx_seo/base/canonical/canonical_for_ln';

    final const BASE_CANONICAL_ASSOCIATED_TYPES	= 'mageworx_seo/base/canonical/associated_types';

    final const ALL_LENGTH_URL_MAX	= 'mageworx_seo/all/length/url_max';

    final const ALL_LENGTH_META_TITLE_MAX	= 'mageworx_seo/all/length/meta_title_max';

    final const ALL_LENGTH_META_KEYWORDS_MAX	= 'mageworx_seo/all/length/meta_keywords_max';

    final const ALL_LENGTH_META_DESCRIPTION_MAX	= 'mageworx_seo/all/length/meta_description_max';

    final const ALL_LENGTH_H1_MAX	= 'mageworx_seo/all/length/h1_max';

    final const SCOPE_ID = 0;
    /**#@-*/

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Encrypted $encrypted
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Encrypted $encrypted,
        protected ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->configWriter->save(self::XML_SITEMAP_MIRASVIT_BLOG_ADD_BLOG_PAGES, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::XML_SITEMAP_MAGEPLAZA_BLOG_ADD_BLOG_PAGES, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::XML_SITEMAP_AHEADWORKS_BLOG_ADD_BLOG_PAGES, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_VIDEO_INCLUDE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_IMAGE_SOURCE, 'cache', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_ROBOTS_EXCLUSION, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::HOMEPAGE_OPTIMIZE, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::EXCLUDE_OUT_OF_STOCK_PRODUCTS, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ENABLE_VALIDATE_URLS, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ENABLE_ADDITIONAL_LINKS, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CHECK_URLS_AVAILABILITY, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_IMAGE_INCLUDE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ADDITIONAL_LINKS, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_USE_INVERT_REDIRECT, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_USE_IN_PAGER, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_USE_IN_ATTRIBUTE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::USE_PRODUCT_SEO_NAME, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::USE_CATEGORY_SEO_NAME, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ENABLED_CRON_NOTIFY, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CROP_ROOT_CATEGORY, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CROP_META_TITLE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CROP_META_DESCRIPTION, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DELETED_PRODUCT_REDIRECT_TYPE, '301', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DELETED_PRODUCT_REDIRECT_TARGET, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DELETED_PRODUCT_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DELETED_PRODUCT_COUNT_STABLE_DAY, 30, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CUSTOM_KEEP_FOR_DELETED_ENTITIES, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CUSTOM_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::USE_NAME_FOR_TITLE, '2', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::REPLACEMENT_COUNT_PRODUCT, '2', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::REPLACEMENT_COUNT_CMS_PAGE, '2', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::REPLACEMENT_COUNT_CATEGORY, '2', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_ATTRIBUTES, 'short_description,description', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DEFAULT_TARGET, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DEFAULT_STATUS, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DEFAULT_REPLACEMENT_COUNT, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DEFAULT_REFERENCE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DEFAULT_PRIORITY, '50', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::DEFAULT_DESTINATION, 'product_page,category_page,cms_page_content', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::WEBSITE_WEBSITE_USE_SEARCH, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::WEBSITE_TW_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::WEBSITE_RS_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::WEBSITE_OG_IMAGE, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::WEBSITE_OG_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::WEBSITE_NAME, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::WEBSITE_FB_APP_ID, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::WEBSITE_DESCRIPTION, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_TYPE, 'LocalBusiness', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_STREET, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_SHOW_ON_PAGES, 'all', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_SAME_AS_LINKS, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_RS_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_REGION, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_PRICE_RANGE, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_POST_CODE, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_PHONE, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_NAME, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);'mageworx_seo/markup/seller/name';

        $this->configWriter->save(self::SELLER_LOCATION, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_IMAGE, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_FAX, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_EMAIL, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SELLER_DESCRIPTION, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_WEIGHT_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_USE_MULTIPLE_OFFER, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_TW_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_SPECIAL_PRICE_FUNCTIONALITY, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_SKU_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_RS_ENABLED_FOR_SPECIFIC_PRODUCT, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_RS_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_PRODUCT_ID_CODE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_PRICE_VALID_UNTIL_DEFAULT_VALUE, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_OG_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_MODEL_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_MANUFACTURER_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_GTIN_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_GA_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_GA_CSS_SELECTOR, '.description', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_DISABLE_DEFAULT_REVIEW, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_DESCRIPTION_CODE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_CUSTOM_PRORERTY_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_CUSTOM_PRORERTIES, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_CROP_HTML_IN_DESCRIPTION, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_CONDITION_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_COLOR_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_CATEGORY_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_BRAND_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_BEST_RATING, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_ADD_REVIEWS, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PAGE_TW_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PAGE_OG_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PAGE_GA_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PAGE_GA_CSS_SELECTOR, '.cms-content', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::COMMON_TW_USERNAME, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_TW_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_RS_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_ROBOTS_RESTRICTION, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_OG_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_GA_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_GA_CSS_SELECTOR, '.category-description', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_ADD_PRODUCT_OFFERS, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BREADCRUMBS_RS_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::USE_CAT_DISPLAY_MODE, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::TITLE, 'Sitemap', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SHOW_STORES, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SHOW_PRODUCTS, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SHOW_LINKS, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SHOW_CUSTOM_LINKS, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SHOW_CMS_PAGES, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SHOW_CATEGORIES, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::PRODUCT_URL_LENGTH, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_KEYWORDS, 'sitemap, categories, products, pages', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_DESCRIPTION, 'Sitemap Tree: Categories - Products', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CATEGORY_MAX_DEPTH, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::CAT_PROD_SORT_ORDER, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ADDITIONAL_LINKS, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SEO_FILTERS_USE_SEO_FOR_CATEGORY_FILTERS, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::SEO_FILTERS_USE_ON_SINGLE_FILTER, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_PAGER_IN_TITLE, 'beginning', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_PAGER_IN_KEYWORDS, 'beginning', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_PAGER_IN_DESCRIPTION, 'beginning', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_LAYERED_FILTERS_IN_TITLE, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_LAYERED_FILTERS_IN_KEYWORDS, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_LAYERED_FILTERS_IN_DESCRIPTION, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_CUT_TITLE_PREFIX_SUFFIX, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::META_CUT_PREFIX_SUFFIX_PAGES, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::TRAILING_SLASH_HOME_PAGE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::TRAILING_SLASH, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BREADCRUMBS_TYPE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BREADCRUMBS_ENABLED, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BREADCRUMBS_BY_CATEGORY_PRIORITY, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_USE_NEXT_PREV, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_ROBOTS_ROBOTS_FOR_LN_MULTIPLE, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_ROBOTS_NOINDEX_NOFOLLOW_USER_PAGES, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_ROBOTS_NOINDEX_FOLLOW_USER_PAGES, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_ROBOTS_NOINDEX_FOLLOW_PAGES, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_ROBOTS_COUNT_FILTERS_FOR_NOINDEX, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_ROBOTS_CATEGORY_LN_PAGES_ROBOTS, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_ROBOTS_ATTRIBUTE_SETTINGS, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_HREFLANGS_X_DEFAULT_WEBSITE, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_HREFLANGS_SCOPE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_HREFLANGS_ENABLED, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_HREFLANGS_CMS_RELATION_WAY, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_USE_PAGER_IN_CANONICAL, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_USE_CANONICAL, self::YES, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_TRAILING_SLASH_HOME_PAGE, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_TRAILING_SLASH, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_PRODUCT_CANONICAL_URL_TYPE, 'canonical_type_root', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_DISABLE_BY_ROBOTS, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_CROSS_DOMAIN_URL, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_CROSS_DOMAIN_STORE, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_CANONICAL_IGNORE_PAGES, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_CANONICAL_FOR_LN_MULTIPLE, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_CANONICAL_FOR_LN, self::NO, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::BASE_CANONICAL_ASSOCIATED_TYPES, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ALL_LENGTH_URL_MAX, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ALL_LENGTH_META_TITLE_MAX, '70', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ALL_LENGTH_META_KEYWORDS_MAX, '100', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ALL_LENGTH_META_DESCRIPTION_MAX, '150', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->configWriter->save(self::ALL_LENGTH_H1_MAX, '70', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);


        $this->moduleDataSetup->getConnection()->endSetup();
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
