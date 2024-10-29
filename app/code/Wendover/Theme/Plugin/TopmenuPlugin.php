<?php

namespace Wendover\Theme\Plugin;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Topmenu;
use Wendover\MegaMenu\Model\MenuFactory;
use Wendover\MegaMenu\Model\SubMenuFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;

class TopmenuPlugin
{
    public function __construct(
        protected NodeFactory $nodeFactory,
        protected UrlInterface $urlBuilder,
        protected MenuFactory $menuFactory,
        protected SubMenuFactory $subMenuFactory,
        protected SerializerInterface $serialize,
        protected StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @param Topmenu $subject
     * @param $outermostClass
     * @param $childrenWrapClass
     * @param $limit
     * @return void
     */
    public function beforeGetHtml(Topmenu $subject, $outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {

        $mainMenuData = $this->menuFactory->create();
        $collection = $mainMenuData->getCollection()->addFieldToFilter('is_active', '1')->setOrder('sort_order','ASC');
        $subMenuData =$this->subMenuFactory->create();
        $subCollection = $subMenuData->getCollection()->setOrder('submenu_sort_order','ASC');
        $storeUrl = $this->storeManager->getStore()->getBaseUrl();
        foreach ($collection as $mainMenuData) {

            $menuTitle = $mainMenuData->getMenuTitle();
            $id = strtolower(str_replace(" ", "-", $menuTitle));
            $menuUrl = parse_url($mainMenuData->getMenuUrl());
            if (isset($menuUrl['scheme']) && $menuUrl['scheme'] == ('https' || 'http')) {
                $url = $mainMenuData->getMenuUrl();
            } else {
                $url = $storeUrl.$mainMenuData->getMenuUrl();
            }
            $menuClass = $mainMenuData->getMenuClass();
            $level1 = $this->nodeFactory->create(
                [
                    'data' => [
                        'name' => $menuTitle,
                        'id' => $id,
                        'url' => $url,
                        'class' => $menuClass,
                        'has_active' => false,
                        'is_active' => false
                    ],
                    'idField' => 'id',
                    'tree' => $subject->getMenu()->getTree()
                ]
            );
            foreach ($subCollection as $subMenuData) {
                if ($mainMenuData->getMenuId() == $subMenuData->getMenuId()) {
                    $subMenuTitle = $subMenuData->getSubmenuTitle();
                    $sub_id = strtolower(str_replace(" ", "-", $subMenuTitle));
                    $sub_class = $subMenuData->getSubmenuClass();
                    $sub_url = parse_url($subMenuData->getSubmenuUrl());
                    if (isset($sub_url['scheme']) && $sub_url['scheme'] == ('https' || 'http')) {
                        $sub_url = $subMenuData->getSubmenuUrl();
                    } else {
                        $sub_url = $storeUrl.$subMenuData->getSubmenuUrl();
                    }
                    $level2 = $this->nodeFactory->create(
                        [
                            'data' => [
                                'name' => $subMenuTitle,
                                'id' => $sub_id,
                                'url' => $sub_url,
                                'class' => $sub_class,
                                'has_active' => false,
                                'is_active' => false
                            ],
                            'idField' => 'id',
                            'tree' => $subject->getMenu()->getTree()
                        ]
                    );
                    $childMenuData = $subMenuData->getChildMenu();
                    $childSubData = $this->getChildMenuData($childMenuData);
                    foreach ($childSubData as $childData) {
                        $childMenuTitle = $childData["childmenu_title"];
                        $child_id = strtolower(str_replace(" ", "-", $childMenuTitle));
                        $childMenuClass = $childData["childmenu_class"];
                        $child_url = parse_url($childData["childmenu_url"]);
                        if (isset($child_url['scheme']) && $child_url['scheme'] == ('https' || 'http')) {
                            $child_url = $childData["childmenu_url"];
                        } else {
                            $child_url = $storeUrl.$childData["childmenu_url"];
                        }
                        $level3 = $this->nodeFactory->create(
                            [
                                'data' => [
                                    'name' => $childMenuTitle,
                                    'id' => $child_id,
                                    'url' => $child_url,
                                    'class' => $childMenuClass,
                                    'has_active' => false,
                                    'is_active' => false
                                ],
                                'idField' => 'id',
                                'tree' => $subject->getMenu()->getTree()
                            ]
                        );
                        $level2->addChild($level3);
                    }
                    $level1->addChild($level2);
                }
            }
            $subject->getMenu()->addChild($level1);
        }
    }
    public function getChildMenuData($childMenu)
    {
        $childMenuData = $this->serialize->unserialize($childMenu);
        return $childMenuData ?: [];
    }
}
