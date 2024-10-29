<?php
/**
 * This module is used to create custom artwork catalogs,
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection as WishlistFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Perficient\MyCatalog\Api\Data\PageInterfaceFactory;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Framework\Escaper;
use Magento\Company\Api\AuthorizationInterface;
use Magento\Theme\Block\Html\Header\Logo;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Filesystem\Io\File;

/**
 * Class Data
 * @package Perficient\MyCatalog\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Constant for size type.
     */
    const DEFAULT_SIZE_TYPE = '2';

    /**
     * Constant for date format.
     */
    const DATE_FORMAT = 'm/d/Y';

    /**
     * Constant for time format.
     */
    const TIME_FORMAT = 'H:i:s';

    /**
     * Constant for catalog logo path.
     */
    const CATALOG_LOGO_PATH = 'custom_catalog/logos';

    const FILE_MASK = 0777;

    const ART_RENDERER_PATH      = 'ArtRenderer';
    const CUSTOMER_CUSTOMER = "Customer's Customer";
    const STORE_NAME = 'general/store_information/name';
    public $storePaths = [
        'street' => 'general/store_information/street_line1',
        'city' => 'general/store_information/city',
        'postcode' => 'general/store_information/postcode',
        'country' => 'general/store_information/country_id',
        'phone' => 'general/store_information/phone'
    ];

    /**
     * Constant for authorization
     */
    const AUTH_CATALOG = 'Perficient_MyCatalog::view';

    /**
     * Data constructor.
     * @param Context $context
     * @param WishlistFactory $wishlistFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param TimezoneInterface $timezone
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param DirectoryList $directoryList
     * @param AdapterFactory $imageFactory
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param DriverInterface $driver
     * @param Repository $assetRepository
     * @param PageInterfaceFactory $pageModelFactory
     * @param CurrencyFactory $currencyFactory
     * @param RoleInfo $roleInfo
     * @param Escaper $escaper
     * @param AuthorizationInterface $authorization
     * @param Logo $logo
     */
    public function __construct(
        Context $context,
        private readonly WishlistFactory $wishlistFactory,
        private readonly Session $customerSession,
        private readonly UrlInterface $url,
        private readonly TimezoneInterface $timezone,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger,
        private readonly DirectoryList $directoryList,
        private readonly DriverPool $driverPool,
        private readonly AdapterFactory $imageFactory,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly DriverInterface $driver,
        private readonly Repository $assetRepository,
        private readonly PageInterfaceFactory $pageModelFactory,
        private readonly CurrencyFactory $currencyFactory,
        private readonly RoleInfo $roleInfo,
        private readonly Escaper $escaper,
        private readonly AuthorizationInterface $authorization,
        private readonly Logo $logo,

        StringUtils $stringUtils,
        protected File $file
    ) {
        parent::__construct($context);
    }

    /**
     * Method used to configuration value of the given key.
     *
     * @param string $path
     * @return string
     */
    public function getConfigValue($path = ''): ?string
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Method used to get the gallery/wishlist names of logged-in customer.
     *
     * @return array
     */
    public function getGalleryNamesLists()
    {
        $listsArray = [];
        $customerId = $this->customerSession->getCustomerId();
        $wishlists = $this->wishlistFactory;
        $condition = "customer_id = $customerId or FIND_IN_SET($customerId,collaboration_ids)";
        $wishlists->getSelect()->where($condition);
        if (is_countable($wishlists) ? count($wishlists) : 0) {
            // Get the wishlist data.
            foreach ($wishlists as $wishlist) {
                $wishlistId = $wishlist->getWishlistId();
                $wishlistName = __('My Favorites');
                $name = $wishlist->getName();
                if (null != $name && !empty($name)) {
                    $wishlistName = $name;
                }

                $listsArray[$wishlistId] = [
                    'url'         => $this->url->getUrl('wishlist', ['wishlist_id' => $wishlistId]),
                    'wishlist_id' => $wishlistId,
                    'name'        => $wishlistName,
                    'products'    => $wishlist->getItemsCount(),
                    'date'        => $wishlist->getUpdatedAt()
                ];
            }
        }

        return $listsArray;
    }

    /**
     * Method used to get the formatted date.
     *
     * @param $date
     * @return string
     */
    public function getFormattedDate($date)
    {
        return $this->timezone
            ->date($date, null, false)
            ->format(self::DATE_FORMAT);
    }

    /**
     * Method used to get the formatted time.
     *
     * @param $date
     * @return string
     */
    public function getFormattedTime($date)
    {
        return $this->timezone
            ->date($date, null, false)
            ->format(self::TIME_FORMAT);
    }

    /**
     * Method used to get the media url.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            ;
    }

    /**
     * Method used to get the media url.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)
            ;
    }

    /**
     * Method used to check whether the catalog belongs to the current customer or not.
     *
     * @param $catalogId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isCatalogOwner($catalogId)
    {
        $isCatalogOwner = false;
        try {
            $catalog = $this->myCatalogRepository->getById($catalogId);
            $customerId = $this->customerSession->getCustomerId();

            // Check whether the current catalog is opening/editing by the owner or not.
            if (($catalog->getCustomerId() == $customerId) && ($customerId != '')) {
                $isCatalogOwner = true;
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $isCatalogOwner;
    }

    /**
     * Is Shared Catalog
     *
     * @param $catalogId
     * @return bool
     */
    public function isSharedCatalog($catalogId)
    {
        $isSharedCatalog = false;
        try {
            $customerId = $this->customerSession->getCustomerId();
            $isSharedCatalog = $this->myCatalogRepository->isSharedCatalog($catalogId, $customerId);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $isSharedCatalog;
    }


    /**
     * @param $DOMimg
     * @param null $imgObj
     * @return \Magento\Framework\DataObject|null
     */
    public function basicDataFromImg ($DOMimg, $imgObj = null )
    {
        if ($imgObj === null) {
            $imgObj = new \Magento\Framework\DataObject();
        }

        //get url path and imgSkuId
        $imgObj->setData('imgId', $DOMimg->getAttribute('alt'));
        $imgObj->setData('isArt', true);
        if ( !$imgObj->getData('imgId') ) {
            $imgObj->setData('isArt', false);
            $imgObj->setData('imgId',uniqid('uniq'));
        }

        //extract size type if present
        $imgObj->setData('sizeType',2);
        $imgClasses = $DOMimg->getAttribute('class');
        if ( str_contains((string) $imgClasses, 'dropspot-size-type-')) {
            $imgObj->setData('sizeType', $this->extractSizeTypeFromClasses($imgClasses));
        }

        return $imgObj;
    }

    /**
     * @param $DOMimg
     * @param null $imgObj
     * @return null
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function fullDataFromImg ($DOMimg, $imgObj = null )
    {
        if ($imgObj === null) {
            $imgObj = $this->fullDataFromImg($DOMimg);
        }

        $originalImgSrc = $DOMimg->getAttribute('src');
        $imgObj->setData('originalImgSrc', $originalImgSrc);
        //decide if is customized art
        $isRendered = str_contains((string) $imgObj->getData('originalImgSrc'), 'render/index/imgrender') ? true : false;
        $imgObj->setData('isRendered', $isRendered);

        //extract information from imgSrc path
        $imgObj->setData('originalImgSrcPathInfo', new \Magento\Framework\DataObject());
        $imgObj->getData('originalImgSrcPathInfo')->addData($this->file->getPathInfo($originalImgSrc));
        $imgObj->setData('originalImgSrcParsed', new \Magento\Framework\DataObject());
        $imgObj->getData('originalImgSrcParsed')->addData($this->url->parseUrl($originalImgSrc));
        $imgObj->setData('originalImgSrcPath', $imgObj->getData('originalImgSrcParsed')->getData('path'));
        $imgObj->addData(['filename' => $imgObj->getData('originalImgSrcPathInfo')->getData('basename'), 'serverPath' => $this->directoryList->getPath('pub') . $imgObj->getData('originalImgSrcPath')]);

        if ( $isRendered ) {
            //determine server path to img asset based on param string in src
            $paramsString = substr((string) $originalImgSrc, strpos((string) $imgObj->getData('originalImgSrc'),'art_sku'));
            $imgObj->setData('renderedParams', $this->parseUserlandParams($paramsString));
        }

        //add html that specifies this is the first of a particular piece
        $DOMimg->setAttribute('copy', 'false');

        return $imgObj;
    }

    /**
     * @param $imgObj
     * @return mixed
     */
    public function renderAtSize ($imgObj)
    {
        /**
         * TODO: This method will be used when the artwork will be in place.
         */
        return $imgObj->getData('filename');
    }

    /**
     * Sanitize Filename
     *
     * @param   string
     * @param   bool
     * @return  string
     */
    public function sanitizeFilename($str, $relative_path = false)
    {
        $bad = [
            '../',
            '<!--',
            '-->',
            '<',
            '>',
            "'",
            '"',
            '&',
            '$',
            '#',
            '{',
            '}',
            '[',
            ']',
            '=',
            ';',
            '?',
            '%20',
            '%22',
            ' ',
            '%3c',
            // <
            '%253c',
            // <
            '%3e',
            // >
            '%0e',
            // >
            '%28',
            // (
            '%29',
            // )
            '%2528',
            // (
            '%26',
            // &
            '%24',
            // $
            '%3f',
            // ?
            '%3b',
            // ;
            '%3d',
        ];

        if ( ! $relative_path) {
            $bad[] = './';
            $bad[] = '/';
        }

        $str = $this->removeInvisibleCharacters($str, false);
        return stripslashes(str_replace($bad, '', $str));
    }

    /**
     * Method used to remove invisible chars from string.
     *
     * @param $str
     * @param bool $url_encoded
     * @return null|string|string[]
     */
    private function removeInvisibleCharacters($str, $url_encoded = true)
    {
        $non_displayables = [];

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/';  // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';   // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';   // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }

    /**
     * Parse UserlandParams into a varien object
     *
     * @param  string $paramsString
     * @return \Magento\Framework\DataObject
     */
    public function parseUserlandParams($paramsString)
    {
        $parts = explode('/', $paramsString);
        $isKey = true;
        $keys = [];
        $values = [];
        foreach ($parts as $part) {
            if ($isKey) {
                $keys[] = $part;
            } else {
                $values[] = $part;
            }
            $isKey = !$isKey;
        }
        if (count($keys) > count($values)) {
            $values[] = '';
        }
        $map = array_combine($keys, $values);
        $mapObject = new \Magento\Framework\DataObject($map);

        return $mapObject;
    }


    /**
     * Resize any image
     *
     * @param $serverPath
     * @param $sizeType
     * @param bool $isRendered
     * @param int $quality
     * @return  string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function resizeImgGeneric ($serverPath, $sizeType, $isRendered = false, $quality = 1)
    {
        $DS = DIRECTORY_SEPARATOR;

        $filename = basename((string) $serverPath);
        $mediaCacheDir = $this->directoryList->getPath('media') . '/catalog/product/cache/1';
        if (!$this->driver->isDirectory($mediaCacheDir)) {
            try {
                //@mkdir($mediaCacheDir);
                $this->driver->createDirectory($mediaCacheDir, self::FILE_MASK);
            } catch (\Exception $e) {
                $this->logger->error('Resize img error Error: ' . $e->getMessage());
            }
        }

        $specificMediaCacheDir = $mediaCacheDir . $DS . 'sizetype_' . $sizeType;
        if (!$this->driver->isDirectory($specificMediaCacheDir)) {
            //@mkdir($specificMediaCacheDir);
            $this->driver->createDirectory($specificMediaCacheDir, self::FILE_MASK);
        }

        if ($this->driver->isFile($specificMediaCacheDir . $DS . $filename)) {
            $this->driver->touch($specificMediaCacheDir . $DS . $filename);
            return $specificMediaCacheDir . $DS . $filename;
        } else {
            //get current sizes
            if ($this->driver->isFile($serverPath) ) {
                if (str_contains((string) $serverPath, '.jpg')) {
                    $curSize = getimagesizefromstring($serverPath);

                    //set width to resize to
                    $resizeTo = $this->resizeToFromSizeType($sizeType);

                    if ($curSize[0] <= $resizeTo[0]) {
                        //don't enlarge images
                        return $serverPath;
                    } else {
                        //math to determine width
                        if ($resizeTo[0] * $curSize[1] / $curSize[0] >= $resizeTo[1]) {
                            //height is limited
                            $resizeTo[0] = ceil($resizeTo[1] * $curSize[0] / $curSize[1]);
                        } else {
                            //width is limited
                            $resizeTo[1] = ceil($resizeTo[0] * $curSize[1] / $curSize[0]);
                        }

                        //resize image via Varien Image Object
                        $imageResize = $this->imageFactory->create();
                        $imageResize->open($serverPath);
                        $imageResize->constrainOnly(true);
                        $imageResize->keepAspectRatio(true);
                        $imageResize->keepFrame(true);
                        $imageResize->keepTransparency(false);
                        $imageResize->resize($resizeTo[0], $resizeTo[1]);

                        //save image
                        $imageResize->save($specificMediaCacheDir . $DS . $filename);
                        return $specificMediaCacheDir . $DS . $filename;
                    }
                } else// if (strpos($serverPath, '.png') !== false) {
                {
                    $info = $this->file->getPathInfo($specificMediaCacheDir . $DS . $filename);
                    $newPath = $info['dirname'] . $DS . $info['filename'] . '.jpg';
                    return $this->png2jpg($serverPath, $newPath, 60);
                }
            }
        }

    }

    /**
     * Method used to convert image from PNG to JPG
     * @param $originalFile
     * @param $outputFile
     * @param $quality
     * @return mixed
     */
    function png2jpg($originalFile, $outputFile, $quality)
    {
        if ($this->driver->isFile($originalFile) && mime_content_type($originalFile) === 'image/png' ) {
            $image = imagecreatefrompng($originalFile);
            imagejpeg($image, $outputFile, $quality);
            imagedestroy($image);
            return $outputFile;
        } else {
            return $originalFile;
        }
    }

    /**
     * Method used to get the image size to resize.
     *
     * @param $sizeType
     * @return array
     */
    public function resizeToFromSizeType ($sizeType)
    {
        return match ($sizeType) {
            4 => [750, 350, 'width' => 750, 'height' => 350],
            3 => [350, 750, 'width' => 350, 'height' => 750],
            1 => [350, 350, 'width' => 350, 'height' => 350],
            default => [750, 750, 'width' => 750, 'height' => 750],
        };
    }

    /**
     * Method used to get the page data for the current catalog.
     * @param $catalogId
     * @return array
     */
    public function getPageData($catalogId)
    {
        $pageData = [];
        try {
            $pageFactory = $this->pageModelFactory->create();
            $pages = $pageFactory->getCollection()
                ->addFieldToFilter('catalog_id', $catalogId)
                ->setOrder('page_position', 'ASC');

            foreach ($pages as $page) {
                $pageData[$page->getPageId()] = $page->toArray();
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $pageData;
    }

    /**
     * Find the size type from a list of html classes
     *
     * @param   string
     * @return  string
     */
    public function extractSizeTypeFromClasses ($classStr)
    {
        return preg_replace('/.*dropspot-size-type-([\d]).*/','$1',$classStr);
    }

    /**
     * Compare existing size type to new size type and return the larger
     *
     * @param $oldSize
     * @param $newSize
     * @return  int
     */
    public function compareSizeTypes ($oldSize, $newSize)
    {
        /**
         * Size types
         * 1 => quarter
         * 2 => full-size
         * 3 => tall half
         * 4 => wide half
         */
        if ($oldSize == $newSize) { return $oldSize; }

        //catches $oldSize==1 to anything else
        if ($oldSize == 1) { return $newSize; }

        //if newsize is the smallest return the oldsize
        if ($newSize == 1) { return $oldSize; }

        //no matter what if full size return full size
        if ($oldSize == 2) { return $oldSize; }

        //the only available options for 3 or 4 are that the new size will be the other one
        return self::DEFAULT_SIZE_TYPE;
    }

    /**
     * Method used to get the file content.
     *
     * @param $module
     * @param $filePath
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFileContents($module, $filePath)
    {
        $fileId = $module . '::'. $filePath;
        $params = ['area' => 'frontend'];
        $asset = $this->assetRepository->createAsset($fileId, $params);
        try {
            $sourceFile = $asset->getSourceFile();
            return $this->driver->fileGetContents($sourceFile);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Method used to get current currency symbol.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencySymbol()
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $currencyFactory = $this->currencyFactory->create();
        $currency = $currencyFactory->load($currentCurrency);
        return $currency->getCurrencySymbol();
    }

    /**
     * @return bool
     */
    public function isLoggedIn(){
       return $this->customerSession->isLoggedIn();
    }
    /**
     * @return mixed
     */
    public function getCurrentLoggedInCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();

    }

    /**
     * Get current User Role
     */
    public function getCurrentUserRole(): bool
    {
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);
		if(isset($currentUserRole[0])){
		//$currentUserRoleText = html_entity_decode($currentUserRole[0], ENT_QUOTES);
        //$currentUserRoleText = $this->stringUtils->htmlEntityDecode($currentUserRole[0], ENT_QUOTES);
		}
        $currentUserRoleText = $currentUserRole;
        if(self::CUSTOMER_CUSTOMER == $currentUserRoleText){
            return false;
        }
        return true;
    }

    /**
     * Method used to check whether user has access to my catalog or not.
     */
    public function isAllowMyCatalog()
    {
        return $this->authorization->isAllowed(self::AUTH_CATALOG);
    }

	/**
     * Method used to validate customer, whether user is logged-in or not.
     * If not then redirect to login page.
     */
    public function validateCustomer()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl($this->url->getCurrentUrl());
            $this->customerSession->authenticate();
        }
    }

    /**
     * Method used to validate customer, whether user is logged-in or not.
     * If not then redirect to login page.
     */
    public function userLoggedInStatus()
    {
        if ($this->customerSession->isLoggedIn()) {
            return true;
        }
        return false;
    }


    /**
     * @return string
     */
    public function getStoreDetails()
    {
        $outputHtml = '';
        try {
            foreach ($this->storePaths as $key => $path) {
                if ($value = $this->getConfigValue($path)) {
                    if ($key == 'city') {
                        $outputHtml .=  "<span> ".$value.", </span>";
                        continue;
                    }

                    if ($key == 'postcode') {
                        $outputHtml .=  "<span> FL ".$value." </span>";
                        continue;
                    }

                    $outputHtml .=  "<span> ".$value." </span>";
                }
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }

        return $outputHtml;
    }
}
