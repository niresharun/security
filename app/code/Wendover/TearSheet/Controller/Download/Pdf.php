<?php
namespace Wendover\TearSheet\Controller\Download;

use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Store\Model\Information;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Filesystem\Driver\File;
use Perficient\Catalog\Helper\Data as CatalogDataHelper;


class Pdf implements ActionInterface
{
    /**
     * @param StoreManagerInterface $storeManager
     * @param FileFactory $fileFactory
     * @param RequestInterface $request
     * @param ProductRepositoryInterfaceFactory $productRepositoryFactory
     * @param Filesystem $filesystem
     * @param Information $storeInfo
     * @param RegionFactory $regionFactory
     * @param CatalogDataHelper $catalogHelper
     */
    public function __construct(
        private readonly StoreManagerInterface              $storeManager,
        private readonly FileFactory                        $fileFactory,
        private readonly RequestInterface                   $request,
        private readonly ProductRepositoryInterfaceFactory  $productRepositoryFactory,
        private readonly Filesystem                         $filesystem,
        private readonly PricingHelper                      $priceHelper,
        private readonly Information                        $storeInfo,
        private readonly RegionFactory                      $regionFactory,
        private readonly File                               $fileDriver,
        private readonly CatalogDataHelper                  $catalogHelper
    ) {
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     * @throws \Zend_Pdf_Exception
     */
    public function execute()
    {
        $productId = $this->request->getParam('product_id');
        $product = $this->productRepositoryFactory->create()->getById($productId);
        $frameSku = $this->request->getParam('framesku') ?: $product->getData('frame_default_sku');
        $frameSku = $frameSku ?: $product->getData('frame_default_sku_configurable');
        $frame = '';
        if($frameSku) {
            try {
                $frame = $this->productRepositoryFactory->create()->get($frameSku);
            } catch (NoSuchEntityException $e) {
                $frame = '';
            }
        }
        $pdf = new \Zend_Pdf();
        $pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
        $page = $pdf->pages[0];
        $style = new \Zend_Pdf_Style();
        $style->setLineColor(new \Zend_Pdf_Color_Rgb(0,0,0));
        $rootDirectory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        //$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA);
        $font = \Zend_Pdf_Font::fontWithPath(
            $rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/Helvetica.ttf')
        );
        //$fontBold = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $fontBold = \Zend_Pdf_Font::fontWithPath(
            $rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/Helvetica-Bold.ttf')
        );
        $style->setFont($font,13);
        $page->setStyle($style);

        // display Header Logo image
        $this->displayHeaderLogoImage($page, $style, $font);
        // display product image
        $this->displayProductImage($product, $style, $font, $page);
        // display frame image
        $this->displayFrameImage($page, $frame);

        $style->setFont($font, 9);
        $style->setLineWidth(15);
        $page->setStyle($style);
        $page->drawText(__("ITEM NUMBER : "), 635, 370, 'UTF-8');
        $style->setFont($fontBold, 9);
        $page->setStyle($style);
        $page->drawText($product->getSku(), 710, 370, 'UTF-8');

        $style->setFont($font, 9);
        $page->setStyle($style);
        $lineHeight = 350;
        $page->drawText(__("TITLE : "), 671, $lineHeight, 'UTF-8');
        $style->setFont($fontBold, 9);
        $page->setStyle($style);
        $lineHeight = $this->drawLineDynamically($page, $product->getName(), $lineHeight);

        $style->setFont($font, 9);
        $page->setStyle($style);
        $page->drawText(__("OUTWARD DIMENSION (W X H) : "), 563, $lineHeight, 'UTF-8');

        $width = $product->getCustomAttribute('item_width') ? $product->getCustomAttribute('item_width')->getValue() : null;
        $height = $product->getCustomAttribute('item_height') ? $product->getCustomAttribute('item_height')->getValue() : null;
        $style->setFont($fontBold, 9);
        $page->setStyle($style);
        $itemDimension = $width . '" x ' . $height . '"';
        $lineHeight = $this->drawLineDynamically($page, $itemDimension, $lineHeight);
        $frameWidth = $product->getData('frame_width');
        $frameDepth = $product->getData('frame_depth');
        $frameInfo =  $frameWidth.'" x '.$frameDepth.'"';

        if ($product->getTypeId() === 'simple' && $this->catalogHelper->getParentId($product->getId())) {
            $frameInfo = $this->catalogHelper->frameDimension($frame);
        }

        $style->setFont($font, 9);
        $page->setStyle($style);
        if($frame) {
            $page->drawText(__("FRAME (WIDTH X DEPTH) : "), 587, $lineHeight, 'UTF-8');
            $style->setFont($fontBold, 9);
            $page->setStyle($style);
            $lineHeight = $this->drawLineDynamically($page, $frameSku, $lineHeight);
            $style->setFont($fontBold, 9);
            $page->setStyle($style);
            $lineHeight = $lineHeight + 8;
            $lineHeight = $this->drawLineDynamically($page, $frameInfo, $lineHeight);
            $style->setFont($font, 9);
            $page->setStyle($style);
            $page->drawText(__("FRAME FINISH : "), 635, $lineHeight, 'UTF-8');

            $frameType = $frame->getAttributeText('frame_type');
            $frameColor = $frame->getAttributeText('color_frame');
            $frameType = $frameType.' Frame';
            $style->setFont($fontBold, 9);
            $page->setStyle($style);
            $lineHeight = $this->drawLineDynamically($page, $frameType, $lineHeight);
            $lineHeight = $lineHeight + 8;
            $page->drawText($frameColor, 710, $lineHeight, 'UTF-8');
        }

        if ($product->getTypeId() === 'simple' && $this->catalogHelper->getParentId($product->getId()))
        {
            $lineHeight = $lineHeight - 20;
            $grassType = $product->getAttributeText('glass_type');
            $style->setFont($font, 9);
            $page->setStyle($style);
            $page->drawText(__("GLASS : "), 671, $lineHeight, 'UTF-8');
            $style->setFont($fontBold, 9);
            $page->setStyle($style);
            $lineHeight = $this->drawLineDynamically($page, $grassType, $lineHeight);
            $lineHeight = $lineHeight - 5;
            $weight = $product->getData('weight') ? round($product->getData('weight')) : 0 ;
            $style->setFont($font, 9);
            $page->setStyle($style);
            $page->drawText(__("WEIGHT : "), 665, $lineHeight, 'UTF-8');
            $style->setFont($fontBold, 9);
            $page->setStyle($style);
            $lineHeight = $this->drawLineDynamically($page, $weight.' lbs', $lineHeight);
            $lineHeight = $lineHeight -5;
            $style->setFont($font, 9);
            $page->setStyle($style);
            $page->drawText(__("HARDWARE : "), 646, $lineHeight, 'UTF-8');
            $style->setFont($fontBold, 9);
            $page->setStyle($style);
            $lineHeight = $this->drawLineDynamically($page, "ZBar", $lineHeight);
        }

        $lineHeight = $lineHeight - 20;
        $style->setFont($font, 9);
        $page->setStyle($style);
        $page->drawText(__("DESCRIPTION : "), 563, $lineHeight, 'UTF-8');
        $speciality = $product->getCustomAttribute('specialty')?$product->getCustomAttribute('specialty')->getValue():null;
        $style->setFont($fontBold, 9);
        $page->setStyle($style);
        $speciality = explode("\n", wordwrap((string)$speciality, 55));
        $lineHeight = $lineHeight - 20;
        foreach ($speciality as $string) {
            $page->drawText($string, 563, $lineHeight, 'UTF-8');
            $lineHeight = $lineHeight - 20;
        }

        // display footer text
        $style->setFont($font, 10);
        $page->setStyle($style);
        $storeInfo = $this->storeInfo->getStoreInformationObject($this->storeManager->getStore());
        $street = $storeInfo->getData('street_line1');
        $city = $storeInfo->getData('city');
        $region = $this->regionFactory->create()->load($storeInfo->getData('region_id'));
        $postcode = $storeInfo->getData('postcode');
        $country = $storeInfo->getData('country_id');
        $phone = $storeInfo->getData('phone');
        $page->drawText(__("HEADQUARTERS • " . $street . " • " . $city . ", " . $region->getCode() . " " . $postcode . " • " . $country . " " . $phone), ($page->getWidth() / 2) - 180, 10);
        $fileName = $product->getName() . '.pdf';

        $this->fileFactory->create(
            $fileName,
            $pdf->render(),
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }

    /**
     * @param $page
     * @param $style
     * @param $font
     * @return void
     * @throws \Zend_Pdf_Exception
     */
    public function displayHeaderLogoImage($page, $style, $font)
    {
        $logoImagePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/Wendover-Art-Group-logo.jpg');
        if ($this->fileDriver->isExists($logoImagePath)) {
            $logoImage = \Zend_Pdf_Image::imageWithPath($logoImagePath);
            $page->drawImage($logoImage, 15, 550, 350, 570);
        } else {
            $style->setFont($font, 13);
            $style->setFontSize(22);
            $page->setStyle($style);
            $page->drawText(__("WENDOVER ART GROUP"), 15, 550, 'UTF-8');
        }
        $page->drawLine(10, 540, $page->getWidth() - 10, 540);
    }

    /**
     * @param $page
     * @param $frame
     * @return void
     * @throws \Zend_Pdf_Exception
     */
    public function displayFrameImage($page, $frame)
    {
        $frameImageURL = $frame ? $frame->getData('double_corner'): '';
        $frameImagePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/product' . $frameImageURL);
        if ($this->fileDriver->isExists($frameImagePath) && $frameImageURL) {
            // convert png to jpg for pdf doc
            $frameImage = $this->convertPNGToJpg($frameImagePath);
            $page->drawImage($frameImage, 580, 400, 810, 520);
        } else if($frame){
            $framePlaceholderPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/frame-placeholder.jpg');
            $framePlaceholder = \Zend_Pdf_Image::imageWithPath($framePlaceholderPath);
            $page->drawImage($framePlaceholder, 620, 380, 780, 535);
        }
    }

    /**
     * @param $imagePath
     * @return \Zend_Pdf_Resource_Image
     * @throws \Zend_Pdf_Exception
     */
    public function convertPNGToJpg($imagePath)
    {
        if (str_contains(strtolower((string)$imagePath), '.png')) {
            if (exif_imagetype($imagePath) == IMAGETYPE_PNG) {
                $image = imagecreatefrompng($imagePath);
            } else {
                $image = imagecreatefromjpeg($imagePath);
            }
            $imagePath = str_replace(['.png', '.PNG'], '.jpg', (string)$imagePath);
            imagejpeg($image, $imagePath, 70);
            imagedestroy($image);
            $imageWithPath = \Zend_Pdf_Image::imageWithPath($imagePath);
        } else {
            if (exif_imagetype($imagePath) == IMAGETYPE_PNG) {
                $image = imagecreatefrompng($imagePath);
                imagejpeg($image, $imagePath, 70);
                imagedestroy($image);
            }
            $imageWithPath = \Zend_Pdf_Image::imageWithPath($imagePath);
        }
        return $imageWithPath;
    }

    /**
     * @param $product
     * @param $style
     * @param $font
     * @param $page
     * @return void
     * @throws NoSuchEntityException
     * @throws \Zend_Pdf_Exception
     */
    public function displayProductImage($product, $style, $font, $page)
    {
        $style->setFont($font, 10);
        $page->setStyle($style);
        $imgURL = $product->getData('image');
        $sku = $product->getData('sku');
        $type = $this->request->getParam('type')?$this->request->getParam('type'):0;

        if($type == 1){
            $imagePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('tearsheet/savedcanvas/'. $sku.'.jpeg');
        }
        else{
            $imagePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/product' . $imgURL);
        }

        if ($imgURL != '' && $this->fileDriver->isExists($imagePath)) {
            // convert png to jpg for pdf doc
            $image = $this->convertPNGToJpg($imagePath);
        } else {
            $imgURL = $this->storeManager->getStore()->getConfig('catalog/placeholder/image_placeholder');
            $imagePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/product/placeholder/' . $imgURL);
            $image = \Zend_Pdf_Image::imageWithPath($imagePath);
        }
        [$width, $height] = getimagesize($imagePath);
        if ($width > $height) {
            $page->drawImage($image, 30, 120, 540, 460); // Landscape Image
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                $page->drawImage($image, 170, 60, 380, 510); // Long Portrait Image
            } else {
                $page->drawImage($image, 110, 60, 440, 510); // Short Portrait Image
            }
        } else {
            $page->drawImage($image, 50, 80, 520, 480); // Square Image
        }
    }

    /**
     * @param $page
     * @param $data
     * @param $lineHeight
     * @return int|mixed
     */
    public function drawLineDynamically($page, $data, $lineHeight)
    {
        $data = explode("\n", wordwrap((string)$data, 26));
        foreach ($data as $string) {
            $page->drawText($string, 710, $lineHeight, 'UTF-8');
            $lineHeight = $lineHeight - 20;
        }
        return $lineHeight;
    }
}
