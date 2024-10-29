<?php
/**
 * Modify Customer Account Sales Order Navigation
 * @category: Magento
 * @package: Perficient/Seo
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Monika Nemade <Monika. @Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Seo
 */

declare(strict_types=1);

namespace Perficient\Seo\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class CanonicalUpdates
 * @package Perficient\Seo\Setup\Patch\Data
 */
class CanonicalUpdates implements DataPatchInterface
{		
	const SCOPE_ID = 0;
	
    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.0';
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->updateConfigurations();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Update SEO configurations
     */
	public function updateConfigurations()
	{

//Please keep allignment as it is otherwise it may cause issues in robots.txt
$robotsValue =
'User-agent: *
Disallow: /index.php/
Disallow: /*?
Disallow: /checkout/
Disallow: /app/
Disallow: /lib/
Disallow: /*.php$
Disallow: /pkginfo/
Disallow: /report/
Disallow: /var/
Disallow: /catalog/
Disallow: /customer/
Disallow: /sendfriend/
Disallow: /review/
Disallow: /*SID=
Disallow: /customer/account/login/
Disallow: filter_size
Allow: /*?p';

		$defaultRobots = 'NOINDEX,NOFOLLOW';			

		$configSettings = [
			'catalog/seo/category_canonical_tag' => '0',
			'catalog/seo/product_canonical_tag' => '0',
			'mageworx_seo/base/canonical/use_canonical' => '1',
			'mageworx_seo/base/canonical/disable_by_robots' => '1',
			'mageworx_seo/base/canonical/canonical_ignore_pages' => '',
			'mageworx_seo/base/canonical/trailing_slash_home_page' => '0',
			'mageworx_seo/base/canonical/trailing_slash' => '0',
			'mageworx_seo/base/canonical/use_pager_in_canonical' => '0',
			'mageworx_seo/base/canonical/canonical_for_ln' => '0',
			'mageworx_seo/base/canonical/canonical_for_ln_multiple' => '',
			'mageworx_seo/base/canonical/cross_domain_store' => '',
			'mageworx_seo/base/canonical/cross_domain_url' => '',
			'mageworx_seo/base/canonical/product_canonical_url_type' => 'canonical_type_root',
			'mageworx_seo/base/canonical/associated_types' => '',
			'design/search_engine_robots/custom_instructions' => $robotsValue,
			'design/search_engine_robots/default_robots' => $defaultRobots
		];
		
		foreach ($configSettings as $configKey => $configValue) {
			$this->configWriter->save($configKey, $configValue, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
		}	
		
	}

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}