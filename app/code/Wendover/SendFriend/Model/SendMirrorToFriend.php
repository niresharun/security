<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wendover\SendFriend\Model;

use Magento\Catalog\Helper\Image;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException as CoreException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\SendFriend\Helper\Data;
use Magento\SendFriend\Model\SendFriend;
use Magento\Store\Model\StoreManagerInterface;
use Perficient\Catalog\Helper\Data as CatalogHelperData;

class SendMirrorToFriend extends SendFriend
{

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param Image $catalogImage
     * @param Data $sendfriendData
     * @param Escaper $escaper
     * @param RemoteAddress $remoteAddress
     * @param CookieManagerInterface $cookieManager
     * @param StateInterface $inlineTranslation
     * @param CatalogHelperData $catalogHelperData
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param CookieMetadataFactory|null $cookieMetadataFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        Image $catalogImage,
        Data $sendfriendData,
        Escaper $escaper,
        RemoteAddress $remoteAddress,
        CookieManagerInterface $cookieManager,
        StateInterface $inlineTranslation,
        protected readonly CatalogHelperData $catalogHelperData,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        CookieMetadataFactory $cookieMetadataFactory = null,
    ) {
        $this->cookieMetadataFactory = $cookieMetadataFactory
            ?? ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $transportBuilder,
            $catalogImage,
            $sendfriendData,
            $escaper,
            $remoteAddress,
            $cookieManager,
            $inlineTranslation,
            $resource,
            $resourceCollection,
            $data,
            $cookieMetadataFactory
        );
    }

    /**
     * Sends email to recipients
     *
     * @return $this
     * @throws CoreException
     */
    public function send()
    {
        if ($this->isExceedLimit()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You\'ve met your limit of %1 sends in an hour.', $this->getMaxSendsToFriend())
            );
        }

        $this->inlineTranslation->suspend();

        $message = nl2br($this->_escaper->escapeHtml($this->getSender()->getMessage()));
        $sender = [
            'name' => $this->_escaper->escapeHtml($this->getSender()->getName()),
            'email' => $this->_escaper->escapeHtml($this->getSender()->getEmail()),
        ];

        foreach ($this->getRecipients()->getEmails() as $k => $email) {
            $name = $this->getRecipients()->getNames($k);
            $product = $this->getProduct();
            $productImage = $this->_catalogImage->init($product, 'sendfriend_small_image');
            $this->_transportBuilder->setTemplateIdentifier(
                $this->_sendfriendData->getEmailTemplate()
            )->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )->setFromByScope(
                'general'
            )->setReplyTo(
                $sender['email'],
                $sender['name']
            )->setTemplateVars([
                'name' => $name,
                'email' => $email,
                'product_name' => $this->getProduct()->getName(),
                'product_url' => $this->catalogHelperData->getMirrorProductUrl((int)$product->getId()),
                'message' => $message,
                'sender_name' => $sender['name'],
                'sender_email' => $sender['email'],
                'product_image' => $productImage->getType() !== null
                    ? $productImage->getUrl()
                    : $productImage->getDefaultPlaceholderUrl()
            ])->addTo(
                $email,
                $name
            );
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        }

        $this->inlineTranslation->resume();

        $this->_incrementSentCount();

        return $this;
    }
}
