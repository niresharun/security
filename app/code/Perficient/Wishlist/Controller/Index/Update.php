<?php
/**
 * overide for side mark
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Hiral Jain<hiral.jain@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Controller for updating wishlists
 */
class Update extends \Magento\Wishlist\Controller\Index\Update
{
    public function __construct(
        Action\Context                                         $context,
        \Magento\Framework\Data\Form\FormKey\Validator         $formKeyValidator,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Wishlist\Model\LocaleQuantityProcessor        $quantityProcessor,
        private readonly SerializerInterface                   $serializer,
        private readonly SchemaSetupInterface                  $schemaSetupInterface
    )
    {
        $this->_formKeyValidator = $formKeyValidator;
        $this->wishlistProvider = $wishlistProvider;
        $this->quantityProcessor = $quantityProcessor;
        parent::__construct($context, $formKeyValidator, $wishlistProvider, $quantityProcessor);
    }

    /**
     * Update wishlist item comments
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        $post = $this->getRequest()->getPostValue();
        $resultRedirect->setPath('*', ['wishlist_id' => $wishlist->getId()]);
        if (!$post) {
            return $resultRedirect;
        }

        if (isset($post['description']) && is_array($post['description'])) {
            $updatedItems = 0;


            foreach ($post['description'] as $itemId => $description) {

                $item = $this->_objectManager->create(\Magento\Wishlist\Model\Item::class)->load($itemId);
                if ($item->getWishlistId() != $wishlist->getId()) {
                    continue;
                }

                // Used sql queries to update the wishlist_item_option table value field.

                if ($description != '') {
                    try {
                        $this->schemaSetupInterface->startSetup();
                        $tableName = $this->schemaSetupInterface->getTable('wishlist_item_option');

                        // Used sql queries to update the wishlist_item_option table value field.
                        $select = $this->schemaSetupInterface->getConnection()->select()->from(
                            ['opt' => $tableName],
                            ['code', 'value']
                        )->where(
                            "opt.wishlist_item_id = ?", $itemId
                        );

                        $tableData = $this->schemaSetupInterface->getConnection()->fetchAll($select);
                        if (!empty($tableData)) {
                            foreach ($tableData as $Optkey => $Optvalue) {
                                $arrayData = [];
                                $updateFlag = false;
                                if (!empty($Optvalue['code']) && !empty($Optvalue['value'])) {
                                    $arrayData = $this->serializer->unserialize($Optvalue['value']);
                                    if ($Optvalue['code'] == 'info_buyRequest' && !empty($arrayData['pz_cart_properties'])) {
                                        $pzArrayData = $this->serializer->unserialize($arrayData['pz_cart_properties']);
                                        if (isset($pzArrayData['Side Mark'])) {
                                            if (trim((string)$pzArrayData['Side Mark']) != trim((string)$description)) {
                                                $updateFlag = true;
                                                $pzArrayData['Side Mark'] = (string)$description;
                                                $updatedPzValue = $this->serializer->serialize($pzArrayData);
                                                $arrayData['pz_cart_properties'] = $updatedPzValue;
                                            }
                                        }
                                    }

                                    if ($Optvalue['code'] == 'additional_options') {
                                        $updatedArrData = [];
                                        foreach ($arrayData as $adKey => $adValue) {
                                            if ($adValue['label'] == 'Side Mark' && trim((string)$adValue['value']) != trim((string)$description)) {
                                                $updateFlag = true;
                                                $adValue['value'] = (string)$description;
                                            }
                                            $updatedArrData[] = $adValue;
                                        }
                                        $arrayData = $updatedArrData;
                                    }

                                    try {
                                        if ($updateFlag == true) {
                                            // Used sql queries to update the wishlist_item_option table value field.
                                            $this->schemaSetupInterface->getConnection()->update(
                                                $tableName,
                                                ['value' => $this->serializer->serialize($arrayData)],
                                                '`wishlist_item_id`' . ' = ' . $itemId . ' AND `code`' . ' LIKE ' . ' "' . $Optvalue['code'] . '" '
                                            );

                                            $this->messageManager->addSuccessMessage(
                                                __('%1 has been updated in your Wish List.', $item->getProduct()->getName())
                                            );
                                            $updatedItems++;
                                        }

                                    } catch (\Exception) {
                                        $this->messageManager->addErrorMessage(
                                            __(
                                                'Can\'t save description %1',
                                                $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($description)
                                            )
                                        );

                                    }
                                }
                            }
                        }

                        $this->schemaSetupInterface->endSetup();
                    } catch (\Exception) {
                        $this->messageManager->addErrorMessage(
                            __(
                                'Can\'t save description %1',
                                $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($description)
                            )
                        );
                    }
                }


                $qty = null;
                if (isset($post['qty'][$itemId])) {
                    $qty = $this->quantityProcessor->process($post['qty'][$itemId]);
                }
                if ($qty === null) {
                    $qty = $item->getQty();
                    if (!$qty) {
                        $qty = 1;
                    }
                } elseif (0 == $qty) {
                    try {
                        $item->delete();
                    } catch (\Exception $e) {
                        $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                        $this->messageManager->addErrorMessage(__('We can\'t delete item from Wish List right now.'));
                    }
                }

                // Check that we need to save
                if ($item->getQty() == $qty) {
                    continue;
                }
                try {
                    $item->setQty($qty)->save();
                    $this->messageManager->addSuccessMessage(
                        __('%1 has been updated in your Wish List.', $item->getProduct()->getName())
                    );
                    $updatedItems++;
                } catch (\Exception) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Can\'t save description %1',
                            $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($description)
                        )
                    );
                }
            }

            // save wishlist model for setting date of last update
            if ($updatedItems) {
                try {
                    $wishlist->save();
                    $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();
                } catch (\Exception) {
                    $this->messageManager->addErrorMessage(__('Can\'t update wish list'));
                }
            }
        }

        if (isset($post['save_and_share'])) {
            $resultRedirect->setPath('*/*/share', ['wishlist_id' => $wishlist->getId()]);
        }

        return $resultRedirect;
    }
}
