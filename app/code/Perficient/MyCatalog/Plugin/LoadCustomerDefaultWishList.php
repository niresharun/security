<?php
/**
 * Added to handle product surcharge if minimum order amount not met by customer
 * @category: Magento
 * @package: Perficient/Order
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Order
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Plugin;

use Magento\Wishlist\Model\WishlistFactory;
use Perficient\MyCatalog\Helper\Data as MyCatalogHelper;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Wishlist\Controller\Index\Index;
use Magento\Framework\App\RequestInterface;

class LoadCustomerDefaultWishList
{
    /**
     * LoadCustomerDefaultWishList constructor.
     * @param WishlistFactory $wishListFactory
     * @param RedirectFactory $redirectFactory
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly MyCatalogHelper $myCatalogHelper,
        private readonly WishlistFactory $wishListFactory,
        private readonly RedirectFactory $redirectFactory,
        protected RequestInterface $request,
        array $data = []
    ) {
  }
    /**
     * @param int $customerId
     */
    public function getWishListByCustomerId()
    {
        if(!$this->myCatalogHelper->isLoggedIn()){
            return true;
        }

         $customerId =$this->myCatalogHelper->getCurrentLoggedInCustomerId();
        $wishlist = $this->wishListFactory->create()->loadByCustomerId($customerId, true);
        if(!$wishlist->getId()){
            return true;
        }
       return $wishlist->getId();
    }

    public function afterExecute(Index $subject, $result)
    {
        $params = [];
        $checkWishListParam = $this->request->getParam('wishlist_id');
        if(isset($checkWishListParam)){
          return $result;
        }
        $currentCustomerWishListId = $this->getWishListByCustomerId();
        $resultRedirect = $this->redirectFactory->create();
        $params['wishlist_id'] = $currentCustomerWishListId;
        $resultRedirect->setPath('wishlist/index/index',$params);
        return $resultRedirect;


    }
}
