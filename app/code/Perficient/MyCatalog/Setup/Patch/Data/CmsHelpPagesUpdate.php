<?php
/**
 * This file is used to Update Creating Catalog Help page
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Divya Sree <divya.sree@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Setup\Patch\Data;

use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class CmsHelpPagesUpdate
 * @package Perficient\MyCatalog\Setup\Patch\Data
 */
class CmsHelpPagesUpdate implements DataPatchInterface
{
    /**
     * CmsHelpPagesUpdate constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Factory $configFactory
     * @param PageFactory $pageFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Factory $configFactory,
        private readonly PageFactory $pageFactory
    ){
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
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->updateCatalogCreateHelp();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Update Catalog create.
     */
    private function updateCatalogCreateHelp()
    {
        $cmsBrandBlocks = [
            'title' => 'Create Catalog Help',
            'identifier' => 'create-catalog-help',
            'stores' => [0],
            'page_layout' => '1column',
            'is_active' => 1,
            'content' => '<h1>Creating Catalogs</h1>
<p>With the Catalog Creator you can take artwork from project galleries and create catalogs to share with clients. The tools allow you set custom pricing (or display no pricing) and drag and drop artwork and place it in any sequence you choose. You can download, print or email your catalog to share with others.</p>
<p>All your catalogs can be viewed at the <a href="/mycatalog">My Catalogs</a> link, within <a href="/customer/account">My Account</a>.</p>
<h2>Catalog Creator Help</h2>
<p>You&rsquo;ll notice three main areas on the Create Catalog page:</p>
<p><strong>CATALOG NAVIGATOR&nbsp;</strong>- This area at the top provides an overview of all your pages, helps you navigate to individual pages, edit covers and set pricing.</p>
<p><strong>PAGE EDITOR</strong> &ndash; in the middle section of the Create Catalog page you can add or delete pages, choose the layout of each page and drag artwork from the Art Selector into the pages as you create them.</p>
<p><strong>ART SELECTOR</strong> &ndash; Along the bottom, the Art Selector displays artwork you&rsquo;ve stored in your galleries; use those images to create the pages of your catalog.</p>
<p>View your galleries at the <a href="/wishlist">My Gallery</a> link.</p>
<h2>Catalog Navigator Tools</h2>
<p><strong>ACTION</strong> - Click here to create a PDF of the catalog, print it or delete it.</p>
<p><strong>EDIT COVER</strong> &ndash; This takes you to the Catalog Setup page where you can edit the front and back covers of the catalog.</p>
<p><strong>PAGE NAVIGATOR</strong> &ndash; With the page navigator, you can see all the pages of your catalog; the page you&rsquo;re currently working on will be highlighted. Click on a page to edit it.</p>
<p>You can change the order of pages by clicking and dragging them here. A subtle line will appear between the pages to display the placement.</p>
<p><strong>SHOW PRICING/HIDE PRICING</strong> - This button will show or hide prices in your catalog. If you choose to show prices, they will display along with the product information below the images on the catalog pages. If you don&rsquo;t want to include prices in your catalog, choose hide prices.</p>
<p>Set price multiplier &ndash; The <strong>PRICING</strong> link includes a price multiplier option you can use to set custom pricing. With the multiplier, you can choose a factor ranging from 1x to 4x; prices in your catalog will be multiplied by the amount you choose.</p>
<p><strong>SAVE</strong> &ndash; Simply click or tap this button to save your catalog. (Your catalog is also saved any time you switch to another page or add a page).</p>
<h2>Page Editor Tools</h2>
<p><strong>PAGE LAYOUT OPTIONS</strong> - Click or tap the page icon on the left to choose from eight available layouts for the pages of your catalog. Each page layout has slots where you can drag artwork from the Art Selector.</p>
<p><strong>PRODUCT DETAILS</strong> - As you add artwork to each page, you&rsquo;ll notice space below for specific details about that product. As the page is created, product details will display in the space, including price if applicable and side mark.</p>
<h2>Art Selector Tools</h2>
<p>A "carousel" at the bottom of the page displays all the artwork you have saved to the Project that this catalog is part of. Choose artwork from the selector to drag to the open slots in pages in the Page Editor. You&rsquo;ll notice a visual cue to confirm that when you let go, the artwork will drop into the slot.</p>
<p>You can remove artwork from a slot by simply clicking on it again and dragging it out of the slot. You can place it in another slot or on another page.</p>
<p>The artwork remains in your Artwork Selector, even when placed on a catalog page so you can select it and place it again.</p>'
        ];
        try {

            $myBlockTemplate = $this->pageFactory->create()->load(
                $cmsBrandBlocks['identifier'],
                'identifier'
            );

            if (!$myBlockTemplate->getId()) {
                $myBlockTemplate->setData($cmsBrandBlocks)->save();
            } else {
                $myBlockTemplate->setContent($cmsBrandBlocks['content'])->save();
            }

        } catch (\Exception $e) {
            throw $e;
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
