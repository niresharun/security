<?php
/**
 * UI componant override to remove duplicate, delete action
 *
 * @category: Perficient's Modules
 * @package: Perficient\RolesPermission
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
 * @keywords: Company template for roles permission
 */
declare(strict_types=1);
namespace Perficient\RolesPermission\Ui\Component\Listing\Role\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class ActionsEdit
 */
class ActionsEdit extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected UrlInterface $urlBuilder,
        private readonly \Magento\Company\Api\AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!$this->authorization->isAllowed('Magento_Company::roles_edit') || !isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')]['edit'] = [
                'href' => $this->urlBuilder->getUrl(
                    'company/role/edit',
                    ['id' => $item['role_id']]
                ),
                'label' => __('Edit'),
                'hidden' => false,
            ];
        }
        return $dataSource;
    }
}
