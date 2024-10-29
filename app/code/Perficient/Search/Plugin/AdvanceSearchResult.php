<?php
/**
 * Modify catalog product search
 * @category: Magento
 * @package: Perficient/Search
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Search
 */
declare(strict_types=1);

namespace Perficient\Search\Plugin;

use Magento\CatalogSearch\Controller\Advanced\Result;
use Magento\Framework\App\ResponseInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AdvanceSearchResult
 * @package Perficient\Search\Plugin
 */
class AdvanceSearchResult
{
    /**
     * AdvanceSearchResult constructor.
     * @param ResponseInterface $response
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        protected ResponseInterface $response,
        protected StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @param Result $subject
     * @return mixed
     */
    public function beforeExecute(Result $subject)
    {
        $redirectUrl = $this->storeManager->getStore()->getBaseUrl();
        $this->response->setRedirect($redirectUrl)->sendResponse();
    }
}
