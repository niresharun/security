<?php
/**
 * This module is used to bulk image upload
 *
 * @category:  Magento
 * @package:   Perficient/Base
 * @copyright: Copyright  - 2020 Magento, Inc. All rights reserved.
 * @license:   Magento Enterprise Edition (MEE) license
 * @author:    Vijayashanthi M
 * @project:   Wendover
 * @keywords:  Module Perficient_Base
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Perficient\Base\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\UrlInterface;

/**
 * Class Result
 *
 * @package Perficient\Base\Block
 */
class GetData extends Template
{
    /**
     * Result Constructor
     * @param Context $context
     * @param FormKey $formKey
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        Context                         $context,
        FormKey                         $formKey,
        protected StoreManagerInterface $storeManager,
        protected UrlInterface          $backendUrl
    )
    {
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getBaseUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * @throws LocalizedException
     */
    public function getFormUrl(): string
    {
        return $this->backendUrl->getUrl("customutlrewrite/index/importurlrewrite");
    }

    /**
     * Get store identifier
     *
     * @throws NoSuchEntityException
     */
    public function getStoreId(): int
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get form key
     *
     * @throws LocalizedException
     */
    public function getFormKey(): string
    {
         return $this->formKey->getFormKey();
    }
}

