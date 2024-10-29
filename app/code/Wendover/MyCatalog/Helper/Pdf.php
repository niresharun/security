<?php

namespace Wendover\MyCatalog\Helper;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use PdfcrowdException;
use Perficient\MyCatalog\Api\Data\MyCatalogInterfaceFactory;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Perficient\MyCatalog\Helper\Data;
use Zend_Pdf;
use Zend_Pdf_Color_Rgb;
use Zend_Pdf_Font;
use Zend_Pdf_Image;
use Zend_Pdf_Page;
use Zend_Pdf_Style;
use Magento\Framework\Filesystem\Io\File as IoFile;
class Pdf extends AbstractHelper
{
    const CATALOG_LOGO_PATH = 'custom_catalog/logos';
    const FILE_MASK = 0777;
    protected int $x = 595;
    protected int $y = 842;

    /**
     * Data constructor.
     *
     * @param Context                      $context
     * @param Filesystem                   $filesystem
     * @param FileFactory                  $fileFactory
     * @param File                         $fileDriver
     * @param MyCatalogInterfaceFactory    $myCatalogFactory
     * @param Json                         $json
     * @param Data                         $helper
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param StoreManagerInterface        $storeManager
     * @param DriverInterface              $driver
     * @param DirectoryList                $directoryList
     * @param Session                      $customerSession
     * */
    public function __construct(
        Context $context,
        private readonly Filesystem                     $filesystem,
        private readonly FileFactory                    $fileFactory,
        private readonly File                           $fileDriver,
        private readonly MyCatalogInterfaceFactory      $myCatalogFactory,
        private readonly Json                           $json,
        private readonly Data                           $helper,
        private readonly MyCatalogRepositoryInterface   $myCatalogRepository,
        private readonly StoreManagerInterface          $storeManager,
        private readonly DriverInterface                $driver,
        private readonly DirectoryList                  $directoryList,
        private readonly Session                        $customerSession,
        protected IoFile                                $file,
    ) {
        parent::__construct($context);
    }

    /**
     * Method used to generate PDF from the html file.
     *
     * @param string $file
     * @param int $catalogId
     * @param int $print
     * @param bool $getPath
     * @return string
     * @throws FileSystemException
     * @throws NoSuchEntityException
     * @throws PdfcrowdException
     */
    public function createPdf($file, $catalogId, $print, $getPath = false, $usePhar = false)
    {
        $userid = $this->customerSession->getCustomerId();
        $uniqueNamespace = 'my_catalog_' . $userid . '_' . time();
        $myCatalog = $this->myCatalogFactory->create();
        $catalogImages = $myCatalog->getGalleryImagesPdf($catalogId);
        $pageData = $this->helper->getPageData($catalogId);
        $catalogData = $this->myCatalogRepository->getById($catalogId);
        $pdf = new Zend_Pdf();
        $pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
        $page = $pdf->pages[0];
        $style = new Zend_Pdf_Style();
        $style->setLineColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        $rootDirectory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        $font = \Zend_Pdf_Font::fontWithPath(
            $rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/Helvetica.ttf')
        );
        $fontBold = \Zend_Pdf_Font::fontWithPath(
            $rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/Helvetica-Bold.ttf')
        );
        // Front Page
        $this->frontPage($page, $font, $style, $catalogData);
        $pageNo = 1;
        foreach ($pageData as $singlePage) {
            $pdf->pages[] = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $page = $pdf->pages[$pageNo];
            switch ($singlePage['page_template_id']) {
                case 1:
                    $this->template1($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo);
                    break;
                case 2:
                    $this->template2($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo);
                    break;
                case 3:
                    $this->template3($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo);
                    break;
                case 4:
                    $this->template4($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo);
                    break;
                case 5:
                    $this->template5($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo);
                    break;
                case 6:
                    $this->template6($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo);
                    break;
                case 7:
                    $this->template7($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo);
                    break;
                case 8:
                    $this->template8($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo);
                    break;
            }
            $pageNo++;
        }
        $this->endPage($page, $pdf, $font, $style, $catalogData, $pageNo);

        if ($print == 1) {
            $this->fileFactory->create($file. '.pdf', $pdf->render(), DirectoryList::VAR_DIR, 'application/pdf');
        }
        else {
            $dynamicPath ='pdf/'. date('d-m-Y'). '/';
            $filename = $this->sanitizeFilename($file) . '.pdf';
            $folderPath =$this->directoryList->getPath("media") . '/' . self::CATALOG_LOGO_PATH . '/' .  $dynamicPath;
            $this->file->mkdir($folderPath, 0775);
            $this->file->chmod($folderPath, 0777);
            $path = $this->directoryList->getPath("media") . '/' . self::CATALOG_LOGO_PATH . '/' .  $dynamicPath  . $uniqueNamespace . '_' . $filename;
            $pdfUrl = $this->getMediaUrl(). self::CATALOG_LOGO_PATH . '/' . $dynamicPath  . $uniqueNamespace . '_' . $filename;
            $pdf->save($path);

            if ($getPath) {
                return $pdfUrl;
            }

        }
    }

    /**
     * Method used to Create Front Page.
     *
     * @param string $page
     * @param $font
     * @param $style
     * @param $catalogData
     * @return string
     * @throws NoSuchEntityException
     */
    public function frontPage($page, $font, $style, $catalogData)
    {
        // display Header Logo image
        $logo = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('custom_catalog/logos' . $catalogData->getLogoImage());
        if ($this->fileDriver->isExists($logo) && $catalogData->getLogoImage()) {
            $logoImage = \Zend_Pdf_Image::imageWithPath($logo);
            $page->drawImage($logoImage, 190, 560, 410, 780);
        } else {
            $style->setFont($font, 13);
            $style->setFontSize(22);
            $page->setStyle($style);
            $page->drawText(__("WENDOVER ART GROUP"), 166, 550, 'UTF-8');
        }
        $page->drawLine(70, 530, $page->getWidth() - 70, 530);
        // Title
        $style->setFont($font, 25);
        $page->setStyle($style);

        if(strlen($catalogData->getCatalogTitle())  <= 56)
        {
            $this->drawCenteredText($page,$catalogData->getCatalogTitle(),500);
        }else{
            $lines = $this->drawCenteredLongText($catalogData->getCatalogTitle());
            $pageline = 500;
            foreach($lines as $title)
            {
                $this->drawCenteredText($page,$title,$pageline);
                $pageline = $pageline-25;
            }
        }
    }

    public function drawCenteredLongText($text)
    {
        $newarray = explode(" ",$text);
        $newLine = [];
        $newtext ='';
        for($i=0; $i<count($newarray); $i++)
        {
            $word = $newarray[$i];
            if($newtext)
            {
                $newtext.= ' '.$word;
            }else
            {
                $newtext .= $word;
            }
            if(strlen($newtext) > 45 && strlen($newtext) < 50){

                if(strlen($text) > 100)
                {
                    $newLine[] = $newtext.'...';
                }
                else
                {
                    $newLine[] = $newtext;
                    $bal_text = str_replace($newtext,"",$text);
                    $newtext = '';
                    if($bal_text <= 50){
                        $newLine[] = $bal_text;
                    }
                }

            }
        }
        return $newLine;
    }



    /**
     * Method used to align center the text.
     *
     * @param $page
     * @param $text,
     * @param $bottom
     * @return string
     * @throws NoSuchEntityException
     */
    public function drawCenteredText($page, $text, $bottom)
    {
        $text_width = $this->getTextWidth($text, $page->getFont(), $page->getFontSize());
        $box_width = $page->getWidth();
        $left = ($box_width - $text_width) / 2;
        $page->drawText($text, $left, $bottom, 'UTF-8');
    }

    /**
     * Method used to getTextWidth.
     *
     * @param $text
     * @param $font
     * @param $font_size
     * @return bool
     * @throws NoSuchEntityException
     */
    public function getTextWidth($text, $font, $font_size)
    {
        $drawing_text = $text;
        $text_width = 0;
        $characters = [];
        if($drawing_text) {
            for ($i = 0; $i < strlen($drawing_text); $i++) {
                $characters[] = ord ($drawing_text[$i]);
            }
            $glyphs        = $font->glyphNumbersForCharacters($characters);
            $widths        = $font->widthsForGlyphs($glyphs);
            $text_width   = (array_sum($widths) / $font->getUnitsPerEm()) * $font_size;
        }
        return $text_width;
    }

    /**
     * Method used to check whether the catalog belongs to the current customer or not.
     *
     * @param $catalogImages
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductImage($catalogImagesDetail)
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/product' . $catalogImagesDetail['url']);
    }

    /**
     * Method used to check whether the catalog belongs to the current customer or not.
     *
     * @param $page
     * @param $style
     * @param $font
     * @param $fontBold
     * @param $data
     * @param $catalogImages
     * @param $counter
     * @param int $pageNo
     * @param $i
     * @return bool
     * @throws NoSuchEntityException
     */
    public function productDetails($page, $style, $font, $fontBold, $data, $catalogImages, $counter, $pageNo, $i)
    {
        switch ($counter) {
            case 1:
                $space = 230;
                break;

            case 2:
                $space = 110;
                break;
            case 3:
                $space = 90;
                break;
            case 4:
                $space = 70;
                break;
            default:
                $space = 95;
                break;
        }
        $pageSize = $page->getWidth() - 100;

        $page->drawLine(55, 210, $page->getWidth() - 55, 210);
        for ($image = 1; $image <= $counter; $image++) {
            if (isset($data['dropspot_' . $image]['item_id'])) {
                $_pId = $data['dropspot_' . $image]['item_id'];
                if(isset($catalogImages[$_pId])) {
                    $catalogImagesDetail = $catalogImages[$_pId]['desc'];
                    $lineNo = $i;
                    //$font = new Zend_Pdf_Resource_Font_Simple_Standard_Courier();
                    $lineNo = $lineNo - 10;
                    $style->setFont($font, 8);
                    $page->setStyle($style);
                    $page->drawText("(" . $image . ")", ($pageSize / $counter) * $image - $space, $lineNo);
                    $lineNo = $lineNo - 10;

                    $style->setFont($fontBold, 7);
                    $page->setStyle($style);
                    $page->drawText($catalogImagesDetail['product_name'], (($pageSize / $counter) * $image) - $space, $lineNo);

                    $lineNo = $lineNo - 10;
                    $style->setFont($fontBold, 7);
                    $page->setStyle($style);
                    $page->drawText("Item #: ", (($pageSize / $counter) * $image - $space), $lineNo);
                    $style->setFont($font, 7);
                    $page->setStyle($style);
                    $page->drawText($catalogImagesDetail['sku'], ((($pageSize / $counter) * $image) - $space) + 25, $lineNo);

                    $lineNo = $lineNo - 10;
                    $style->setFont($fontBold, 7);
                    $page->setStyle($style);
                    $page->drawText("Size: ", (($pageSize / $counter) * $image - $space), $lineNo);
                    $style->setFont($font, 7);
                    $page->setStyle($style);
                    $page->drawText($catalogImagesDetail['size'], ((($pageSize / $counter) * $image) - $space) + 20, $lineNo);

                    if ($catalogImagesDetail['price']) {
                        $lineNo = $lineNo - 10;
                        $style->setFont($fontBold, 7);
                        $page->setStyle($style);
                        $page->drawText("Price: " . $catalogImagesDetail['price'], (($pageSize / $counter) * $image - $space), $lineNo);
                    }

                    if ($catalogImagesDetail['medium']) {
                        $lineNo = $lineNo - 10;
                        $style->setFont($fontBold, 7);
                        $page->setStyle($style);
                        $page->drawText("Medium: ", (($pageSize / $counter) * $image - $space), $lineNo);
                        $style->setFont($font, 7);
                        $page->setStyle($style);
                        $page->drawText($catalogImagesDetail['medium'], ((($pageSize / $counter) * $image) - $space) + 30, $lineNo);
                    }

                    if ($catalogImagesDetail['frame']) {
                        $lineNo = $lineNo - 10;
                        $style->setFont($fontBold, 7);
                        $page->setStyle($style);
                        $page->drawText("Frame: ", (($pageSize / $counter) * $image - $space), $lineNo);
                        $style->setFont($font, 7);
                        $page->setStyle($style);
                        $page->drawText($catalogImagesDetail['frame'], ((($pageSize / $counter) * $image) - $space) + 25, $lineNo);
                    }
                }
            }
        }
        $page->drawLine(55, 65, $page->getWidth() - 55, 65);
        $style->setFont($fontBold, 10);
        $page->setStyle($style);
        $page->drawText("Page " . $pageNo, ($page->getWidth() / 2) - 25, 55);
    }

    /**
     * Method used to Create Template 1
     * @param $page
     * @param $style
     * @param $font
     * @param $fontBold
     * @param $catalogImages
     * @param $singlePage
     * @param int $pageNo
     * @return bool
     * @throws NoSuchEntityException
     */
    public function template1($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo)
    {
        $data = $this->json->unserialize($singlePage['drop_spot_config']);
        for ($counter = 1; $counter <= 4; $counter++) {
            if (isset($data['dropspot_' . $counter]) && isset($data['dropspot_' . $counter]['item_id'])) {
                $_pId = $data['dropspot_' . $counter]['item_id'];
                if(isset($catalogImages[$_pId])) {
                    $catalogImagesDetail = $catalogImages[$_pId];
                    $image = $this->checkImage($this->getProductImage($catalogImagesDetail));
                    $imagePath = \Zend_Pdf_Image::imageWithPath($image);
                    [$width, $height] = getimagesize($image);
                    if ($counter == 1) {
                        $this->temp1UpLeftCalc($page, $imagePath, $width, $height, $style, $font, $counter); //Up-Left image
                    } elseif ($counter == 2) {
                        $this->temp1UpRightCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Up-Right image
                    } elseif ($counter == 3) {
                        $this->temp1DownLeftCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Down-Left image
                    } elseif ($counter == 4) {
                        $this->temp1DownRightCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Down-Right image
                    }
                }
            }
        }
        $this->productDetails($page, $style, $font, $fontBold, $data, $catalogImages, 4, $pageNo, 200);
    }

    /**
     * Method used to Create Template 2
     * @param $page
     * @param $style
     * @param $font
     * @param $fontBold
     * @param $catalogImages
     * @param $singlePage
     * @param int $pageNo
     * @return bool
     * @throws NoSuchEntityException
     */
    public function template2($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo)
    {
        $data = $this->json->unserialize($singlePage['drop_spot_config']);
        if(isset($data['dropspot_1']) &&  isset($data['dropspot_1']['item_id'])) {
            $_pId = $data['dropspot_1']['item_id'];
            $catalogImagesDetail = $catalogImages[$_pId];
            $image = $this->checkImage($this->getProductImage($catalogImagesDetail));
            $imagePath = \Zend_Pdf_Image::imageWithPath($image);
            [$width, $height] = getimagesize($image);
            $this->temp2Calc($page, $imagePath, $width, $height, $style, $font);
            $this->productDetails($page, $style, $font, $fontBold, $data, $catalogImages, 1, $pageNo, 200);
        }
    }

    /**
     * Method used to Create Template 3
     * @param $page
     * @param $style
     * @param $font
     * @param $fontBold
     * @param $catalogImages
     * @param $singlePage
     * @param int $pageNo
     * @return bool
     * @throws NoSuchEntityException
     */
    public function template3($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo)
    {
        $data = $this->json->unserialize($singlePage['drop_spot_config']);
        for ($counter = 1; $counter <= 3; $counter++) {
            if (isset($data['dropspot_' . $counter]) && isset($data['dropspot_' . $counter]['item_id'])) {
                $_pId = $data['dropspot_' . $counter]['item_id'];
                if(isset($catalogImages[$_pId])) {
                    $catalogImagesDetail = $catalogImages[$_pId];
                    $image = $this->checkImage($this->getProductImage($catalogImagesDetail));
                    $imagePath = \Zend_Pdf_Image::imageWithPath($image);
                    [$width, $height] = getimagesize($image);
                    if ($counter == 1) {
                        $this->temp1UpLeftCalc($page, $imagePath, $width, $height, $style, $font, $counter); //Up-Left image
                    } elseif ($counter == 2) {
                        $this->temp1UpRightCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Up-Right image
                    } elseif ($counter == 3) {
                        $this->temp8DownCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Down image
                    }
                }
            }
        }
        $this->productDetails($page, $style, $font, $fontBold, $data, $catalogImages, 3, $pageNo, 200);
    }

    /**
     * Method used to Create Template 4
     * @param $page
     * @param $style
     * @param $font
     * @param $fontBold
     * @param $catalogImages
     * @param $singlePage
     * @param int $pageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function template4($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo)
    {
        $data = $this->json->unserialize($singlePage['drop_spot_config']);
        for ($counter = 1; $counter <= 3; $counter++) {
            if (isset($data['dropspot_' . $counter]) && isset($data['dropspot_' . $counter]['item_id'])) {
                $_pId = $data['dropspot_' . $counter]['item_id'];
                if(isset($catalogImages[$_pId])) {
                    $catalogImagesDetail = $catalogImages[$_pId];
                    $image = $this->checkImage($this->getProductImage($catalogImagesDetail));
                    $imagePath = \Zend_Pdf_Image::imageWithPath($image);
                    [$width, $height] = getimagesize($image);
                    if ($counter == 1) {
                        $this->temp8UpCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Up image
                    } elseif ($counter == 2) {
                        $this->temp1DownLeftCalc($page, $imagePath, $width, $height, $style, $font, $counter); //Down-Left image
                    } elseif ($counter == 3) {
                        $this->temp1DownRightCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Down-Right image
                    }
                }
            }
        }
        $this->productDetails($page, $style, $font, $fontBold, $data, $catalogImages, 3, $pageNo, 200);
    }

    /**
     * Method used to Create Template 5
     * @param $page
     * @param $style
     * @param $font
     * @param $fontBold
     * @param $catalogImages
     * @param $singlePage
     * @param int $pageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function template5($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo)
    {

        $data = $this->json->unserialize($singlePage['drop_spot_config']);
        for ($counter = 1; $counter <= 3; $counter++) {
            if (isset($data['dropspot_' . $counter]) && isset($data['dropspot_' . $counter]['item_id'])) {
                $_pId = $data['dropspot_' . $counter]['item_id'];
                if(isset($catalogImages[$_pId])) {
                    $catalogImagesDetail = $catalogImages[$_pId];
                    $image = $this->checkImage($this->getProductImage($catalogImagesDetail));
                    $imagePath = Zend_Pdf_Image::imageWithPath($image);
                    [$width, $height] = getimagesize($image);
                    if ($counter == 1) {
                        $this->temp7LeftCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Left image
                    } elseif ($counter == 2) {
                        $this->temp1UpRightCalc($page, $imagePath, $width, $height, $style, $font, $counter); //Up-Right image
                    } elseif ($counter == 3) {
                        $this->temp1DownRightCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Down-Right image
                    }
                }

            }
        }
        $this->productDetails($page, $style, $font, $fontBold, $data, $catalogImages, 3, $pageNo, 200);
    }

    /**
     * Method used to Create Template 6
     * @param $page
     * @param $style
     * @param $font
     * @param $fontBold
     * @param $catalogImages
     * @param $singlePage
     * @param int $pageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function template6($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo)
    {
        $data = $this->json->unserialize($singlePage['drop_spot_config']);
        for ($counter = 1; $counter <= 3; $counter++) {
            if (isset($data['dropspot_' . $counter]) && isset($data['dropspot_' . $counter]['item_id'])) {
                $_pId = $data['dropspot_' . $counter]['item_id'];
                if(isset($catalogImages[$_pId])){
                    $catalogImagesDetail = $catalogImages[$_pId];
                    $image = $this->checkImage($this->getProductImage($catalogImagesDetail));
                    $imagePath = \Zend_Pdf_Image::imageWithPath($image);
                    [$width, $height] = getimagesize($image);
                    if ($counter == 1) {
                        $this->temp1UpLeftCalc($page, $imagePath, $width, $height, $style, $font, $counter); //Up-Left image
                    } elseif ($counter == 2) {
                        $this->temp1DownLeftCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Down-Left image
                    } elseif ($counter == 3) {
                        $this->temp7RightCalc($page, $imagePath, $width, $height, $style, $font, $counter);   //Right image
                    }
                }

            }
        }
        $this->productDetails($page, $style, $font, $fontBold, $data, $catalogImages, 3, $pageNo, 200);
    }

    /**
     * Method used to Create Template 7
     * @param $page
     * @param $style
     * @param $font
     * @param $fontBold
     * @param $catalogImages
     * @param $singlePage
     * @param int $pageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function template7($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo)
    {
        $data = $this->json->unserialize($singlePage['drop_spot_config']);
        for ($counter = 1; $counter <= 2; $counter++) {
            if (isset($data['dropspot_' . $counter]) && isset($data['dropspot_' . $counter]['item_id'])) {
                    $_pId = $data['dropspot_'.$counter]['item_id'];
                    if(isset($catalogImages[$_pId])){
                        $catalogImagesDetail = $catalogImages[$_pId];
                        $image = $this->checkImage($this->getProductImage($catalogImagesDetail));
                        $imagePath = Zend_Pdf_Image::imageWithPath($image);
                        [$width, $height] = getimagesize($image);
                        if ($counter == 1) {
                            $this->temp7LeftCalc($page, $imagePath, $width, $height, $style, $font, $counter); //Left image
                        } elseif ($counter == 2) {
                            $this->temp7RightCalc($page, $imagePath, $width, $height, $style, $font, $counter); //Right image
                        }
                    }
            }
        }
        $this->productDetails($page, $style, $font, $fontBold, $data, $catalogImages, 2, $pageNo, 200);
    }

    /**
     * Method used to Create Template 8
     * @param $page
     * @param $style
     * @param $font
     * @param $fontBold
     * @param $catalogImages
     * @param $singlePage
     * @param int $pageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function template8($page, $style, $font, $fontBold, $catalogImages, $singlePage, $pageNo)
    {
        $data = $this->json->unserialize($singlePage['drop_spot_config']);
        for ($counter = 1; $counter <= 2; $counter++) {
            if (isset($data['dropspot_' . $counter]) && isset($data['dropspot_' . $counter]['item_id'])) {
                $_pId = $data['dropspot_' . $counter]['item_id'];
              //  if (array_key_exists($_pId, $catalogImages)) {
                if(isset($catalogImages[$_pId])){
                    $catalogImagesDetail = $catalogImages[$_pId];
                    $image = $this->checkImage($this->getProductImage($catalogImagesDetail));
                    $imagePath = Zend_Pdf_Image::imageWithPath($image);
                    [$width, $height] = getimagesize($image);
                    if ($counter == 1) {
                        $this->temp8UpCalc($page, $imagePath, $width, $height, $style, $font, $counter);//Up image
                    } else {
                        $this->temp8DownCalc($page, $imagePath, $width, $height, $style, $font, $counter);//Down image
                    }
                }

            }
        }
        $this->productDetails($page, $style, $font, $fontBold, $data, $catalogImages, 2, $pageNo, 200);
    }

    /**
     * Method used to Create End Page.
     *
     * @param $page
     * @param $font
     * @param $style
     * @param $catalogData
     * @param int $pageNo
     * @return bool
     * @throws NoSuchEntityException
     */

    public function endPage($page, $pdf, $font, $style, $catalogData, $pageNo)
    {
        $pdf->pages[] = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page = $pdf->pages[$pageNo];
        $page->drawLine(70, 490, $page->getWidth() - 70, 490);

         $line = 480;
         if($catalogData->getName()){
             $style->setFont($font, 10);
             $page->setStyle($style);
             $this->drawCenteredText($page, $catalogData->getName(), $line);
             $line = $line -15;
         }

         if($catalogData->getPhoneNumber()){
             $style->setFont($font, 10);
             $page->setStyle($style);
              $this->drawCenteredText($page, $catalogData->getPhoneNumber(), $line);
             $line = $line- 15;
         }

         if($catalogData->getWebsiteUrl()){
             $style->setFont($font, 10);
             $page->setStyle($style);
             $this->drawCenteredText($page, $catalogData->getWebsiteUrl(), $line);
             $line = $line- 15;
         }

         if($catalogData->getCompanyName()){
             $style->setFont($font, 10);
             $page->setStyle($style);
             $this->drawCenteredText($page, $catalogData->getCompanyName(), $line);
         }



    }
    /**
     * Method used to Check product image
     * @param image
     * **/
    public function checkImage($image){
        if (!$this->fileDriver->isExists($image)) {
            $image = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/product/no_image.jpg');
        }
        return $image;
    }


    /**
     * Method used to  Template 1 Up Left image Calculation
     * @param $page
     * @param $imagePath
     * @param $width
     * @param $height
     * @param $style
     * @param $font
     * @param $imageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function temp1UpLeftCalc($page, $imagePath, $width, $height, $style, $font, $imageNo)
    {
        $style->setFont($font, 8);
        $page->setStyle($style);

        if ($width > $height) {
            $page->drawImage($imagePath, 60, $this->y - 238, $this->x / 2 - 15, $this->y - 85); // Landscape Image
            $page->drawText("(" . $imageNo . ")", 170, $this->y - 248);
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                $page->drawImage($imagePath, 115, $this->y / 2 + 148, $this->x / 2 - 73, $this->y - 50); // Long Portrait Image
                $page->drawText("(" . $imageNo . ")", 165, $this->y / 2 + 138);
            } else {
                $page->drawImage($imagePath, 100, $this->y / 2 + 148, $this->x / 2 - 60, $this->y - 50); // Short Portrait Image
                $page->drawText("(" . $imageNo . ")", 165, $this->y / 2 + 138);
            }
        } else {
            $page->drawImage($imagePath, 57, $this->y - 273, $this->x / 2 - 18, $this->y - 50); // Square Image
            $page->drawText("(" . $imageNo . ")", 160, $this->y - 283);
        }
    }

    /**
     * Method used to  Template 1 Up Right image Calculation
     * @param $page
     * @param $imagePath
     * @param $width
     * @param $height
     * @param $style
     * @param $font
     * @param $imageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function temp1UpRightCalc($page, $imagePath, $width, $height, $style, $font, $imageNo)
    {
        $style->setFont($font, 8);
        $page->setStyle($style);
        if ($width > $height) {
            $page->drawImage($imagePath, $this->x / 2 + 10, $this->y - 238, $this->x - 65, $this->y - 85); // Landscape Image
            $page->drawText("(" . $imageNo . ")", $this->x / 2 + 120, $this->y - 248);
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                $page->drawImage($imagePath, $this->x / 2 + 75, $this->y / 2 + 148, $this->x - 115, $this->y - 50); // Long Portrait Image
                $page->drawText("(" . $imageNo . ")", $this->x / 2 + 125, $this->y / 2 + 138);
            } else {
                $page->drawImage($imagePath, $this->x / 2 + 60, $this->y / 2 + 148, $this->x - 100, $this->y - 50);
                $page->drawText("(" . $imageNo . ")", $this->x / 2 + 120, $this->y / 2 + 138);


            }
        } else {
            $page->drawImage($imagePath, $this->x / 2 + 16, $this->y - 273, $this->x - 57, $this->y - 50); // Square Image
            $page->drawText("(" . $imageNo . ")", $this->x / 2 + 120, $this->y - 283);
        }
    }

    /**
     * Method used to  Template 1 Down Left image Calculation
     * @param $page
     * @param $imagePath
     * @param $width
     * @param $height
     * @param $style
     * @param $font
     * @param $imageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function temp1DownLeftCalc($page, $imagePath, $width, $height, $style, $font, $imageNo)
    {
        $style->setFont($font, 8);
        $page->setStyle($style);
        if ($width > $height) {
            $page->drawImage($imagePath, 60, $this->y - 493, $this->x / 2 - 15, $this->y / 2 + 80);  // Landscape Image
            $page->drawText("(" . $imageNo . ")", 170, $this->y - 503);
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                $page->drawImage($imagePath, 115, $this->y / 2 - 104, $this->x / 2 - 73, $this->y / 2 + 118); // Long Portrait Image
                $page->drawText("(" . $imageNo . ")", 165, $this->y / 2 - 114);
            } else {
                $page->drawImage($imagePath, 100, $this->y / 2 - 113, $this->x / 2 - 60, $this->y / 2 + 110); // Short Portrait Image
                $page->drawText("(" . $imageNo . ")", 165, $this->y / 2 - 123);
            }
        } else {
            $page->drawImage($imagePath, 57, $this->y / 2 - 110, $this->x / 2 - 18, $this->y - 307); // Square Image
            $page->drawText("(" . $imageNo . ")", 160, $this->y / 2 - 120);
        }
    }

    /**
     * Method used to  Template 1 Down Right image Calculation
     * @param $page
     * @param $imagePath
     * @param $width
     * @param $height
     * @param $style
     * @param $font
     * @param $imageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function temp1DownRightCalc($page, $imagePath, $width, $height, $style, $font, $imageNo)
    {
        $style->setFont($font, 8);
        $page->setStyle($style);
        //Down-Right image
        if ($width > $height) {
            $page->drawImage($imagePath, $this->x / 2 + 10, $this->y - 493, $this->x - 65, $this->y / 2 + 80); // Landscape Image
            $page->drawText("(" . $imageNo . ")", $this->x / 2 + 120, $this->y - 503);
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                $page->drawImage($imagePath, $this->x / 2 + 75, $this->y / 2 - 104, $this->x - 115, $this->y / 2 + 118); // Long Portrait Image
                $page->drawText("(" . $imageNo . ")", $this->x / 2 + 125, $this->y / 2 - 114);
            } else {
                $page->drawImage($imagePath, $this->x / 2 + 60, $this->y / 2 - 113, $this->x - 100, $this->y / 2 + 110); // Short Portrait Image
                $page->drawText("(" . $imageNo . ")", $this->x / 2 + 120, $this->y / 2 - 123);
            }
        } else {
            $page->drawImage($imagePath, $this->x / 2 + 16, $this->y / 2 - 110, $this->x - 57, $this->y - 307); // Square Image
            $page->drawText("(" . $imageNo . ")", $this->x / 2 + 120, $this->y / 2 - 120);
        }
    }

    /**
     * Method used to Create Template 2 Single Image Calculation
     *
     * @param $page
     * @param $imagePath
     * @param $width
     * @param $height
     * @param $style
     * @param $font
     * @return void
     */
    public function temp2Calc($page, $imagePath, $width, $height, $style, $font)
    {
        $style->setFont($font, 8);
        $page->setStyle($style);
        if ($width > $height) {
            $page->drawImage($imagePath, 140, $this->y - 382, $this->x - 155, $this->y - 187); // Landscape Image
            $page->drawText("(1)", ($page->getWidth() / 2) - 10, $this->y - 392);
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                $page->drawImage($imagePath, 220, $this->y - 429, $this->x - 230, $this->y - 138); // Long Portrait Image
                $page->drawText("(1)", ($page->getWidth() / 2) - 10, $this->y - 439);
            } else {
                $page->drawImage($imagePath, 148, $this->y - 526, $this->x - 155, $this->y - 52); // Short Portrait Image
                $page->drawText("(1)", ($page->getWidth() / 2) - 10, $this->y - 536);
            }
        } else {
            $page->drawImage($imagePath, 150, $this->y - 442, $this->x - 150, $this->y - 150); // Square Image
            $page->drawText("(1)", ($page->getWidth() / 2) - 10, $this->y - 452);
        }
    }

    /**
     * Method used to  Template 7 Left image Calculation
     * @param $page
     * @param $imagePath
     * @param $width
     * @param $height
     * @param $style
     * @param $font
     * @param $imageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function temp7LeftCalc($page, $imagePath, $width, $height, $style, $font, $imageNo)
    {
        $style->setFont($font, 8);
        $page->setStyle($style);
        if ($width > $height) {
            $page->drawImage($imagePath, 60, $this->y - 363, $this->x / 2 - 15, $this->y - 212); // Landscape Image
            $page->drawText("(" . $imageNo . ")", 165, $this->y - 373);
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                $page->drawImage($imagePath, 115, $this->y / 2 + 23, $this->x / 2 - 73, $this->y - 180); // Long Portrait Image
                $page->drawText("(" . $imageNo . ")", 165, $this->y / 2 + 13);
            } else {
                $page->drawImage($imagePath, 55, $this->y / 2 - 48, $this->x / 2 - 25, $this->y - 106);
                $page->drawText("(" . $imageNo . ")", 160, $this->y / 2 - 58);
            }
        } else {
            $page->drawImage($imagePath, 65, $this->y - 390, $this->x / 2 - 13, $this->y - 170); // Square Image
            $page->drawText("(" . $imageNo . ")", 170, $this->y - 400);
        }
    }

    /**
     * Method used to  Template 7 Right image Calculation
     * @param $page
     * @param $imagePath
     * @param $width
     * @param $height
     * @param $style
     * @param $font
     * @param $imageNo
     * @return string
     * @throws NoSuchEntityException
     */

    public function temp7RightCalc($page, $imagePath, $width, $height, $style, $font, $imageNo)
    {
        $style->setFont($font, 8);
        $page->setStyle($style);
        if ($width > $height) {
            $page->drawImage($imagePath, $this->x / 2 + 10, $this->y - 363, $this->x - 65, $this->y - 212); // Landscape Image
            $page->drawText("(" . $imageNo . ")", $this->x / 2 + 120, $this->y - 373);
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                $page->drawImage($imagePath, $this->x / 2 + 75, $this->y / 2 + 23, $this->x - 115, $this->y - 180); // Long Portrait Image
                $page->drawText("(" . $imageNo . ")", $this->x / 2 + 125, $this->y / 2 + 13);
            } else {

                $page->drawImage($imagePath, $this->x / 2 + 20, $this->y / 2 - 48, $this->x - 55, $this->y - 106); // Short Portrait Image
                $page->drawText("(" . $imageNo . ")", $this->x / 2 + 130, $this->y / 2 - 58);

            }
        } else {
            $page->drawImage($imagePath, $this->x / 2 + 15, $this->y - 390, $this->x - 65, $this->y - 170);// Square Image
            $page->drawText("(" . $imageNo . ")", $this->x / 2 + 120, $this->y - 400);

        }
    }

    /**
     * Method used to  Template 8 Up image Calculation
     * @param $page
     * @param $imagePath
     * @param $width
     * @param $height
     * @param $style
     * @param $font
     * @param $imageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function temp8UpCalc($page, $imagePath, $width, $height, $style, $font, $imageNo)
    {
        $style->setFont($font, 8);
        $page->setStyle($style);
        if ($width > $height) {
            // Landscape Image
            $page->drawImage($imagePath, 187, $this->y - 235, $this->x - 187, $this->y - 85);
            $page->drawText("(" . $imageNo . ")", ($page->getWidth() / 2) - 10, $this->y - 245);
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                // Long Portrait Image
                $page->drawImage($imagePath, 245, $this->y / 2 + 149, $this->x - 245, $this->y - 50);
                $page->drawText("(" . $imageNo . ")", ($page->getWidth() / 2) - 7, $this->y / 2 + 139);
            } else {
                // Short Portrait Image
                $page->drawImage($imagePath, 229, $this->y / 2 + 144, $this->x - 229, $this->y - 55);
                $page->drawText("(" . $imageNo . ")", ($page->getWidth() / 2) - 10, $this->y / 2 + 134);
            }
        } else {
            // Square Image
            $page->drawImage($imagePath, 185, $this->y / 2 + 148, $this->x - 185, $this->y - 50);
            $page->drawText("(" . $imageNo . ")", ($page->getWidth() / 2) - 10, $this->y / 2 + 138);
        }
    }

    /**
     * Method used to  Template 8 Down image Calculation
     * @param $page
     * @param $imagePath
     * @param $width
     * @param $height
     * @param $style
     * @param $font
     * @param $imageNo
     * @return string
     * @throws NoSuchEntityException
     */
    public function temp8DownCalc($page, $imagePath, $width, $height, $style, $font, $imageNo)
    {
        $style->setFont($font, 8);
        $page->setStyle($style);
        if ($width > $height) {
            $page->drawImage($imagePath, 187, $this->y / 2 - 70, $this->x - 187, $this->y / 2 + 82); // Landscape Image
            $page->drawText("(" . $imageNo . ")", ($page->getWidth() / 2) - 10, $this->y / 2 - 80);
        } elseif ($height > $width) {
            $height = $height / 2;
            if ($height > $width) {
                $page->drawImage($imagePath, 245, $this->y / 2 - 109, $this->x - 245, $this->y / 2 + 108); //Long Portrait Image
                $page->drawText("(" . $imageNo . ")", ($page->getWidth() / 2) - 7, $this->y / 2 - 119);
            } else {
                $page->drawImage($imagePath, 229, $this->y / 2 - 110, $this->x - 229, $this->y / 2 + 112);
                $page->drawText("(" . $imageNo . ")", ($page->getWidth() / 2) - 10, $this->y / 2 - 120);
            }
        } else {
            $page->drawImage($imagePath, 185, $this->y - 529, $this->x - 185, $this->y / 2 + 110); // Square Image
            $page->drawText("(" . $imageNo . ")", ($page->getWidth() / 2) - 10, $this->y - 539);
        }
    }

    /**
     * Sanitize Filename
     *
     * @param  string $str
     * @param  bool   $relative_path
     * @return string
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

        if (!$relative_path) {
            $bad[] = './';
            $bad[] = '/';
        }

        $str = $this->removeInvisibleCharacters($str, false);
        return stripslashes(str_replace($bad, '', $str));
    }

    /**
     * Method used to remove invisible chars from string.
     *
     * @param  $str
     * @param  bool $url_encoded
     * @return null|string|string[]
     */
    public function removeInvisibleCharacters($str, $url_encoded = true)
    {
        $non_displayables = [];

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded) {
            // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%0[0-8bcef]/';
            // url encoded 16-31
            $non_displayables[] = '/%1[0-9a-f]/';
        }
        // 00-08, 11, 12, 14-31, 127
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }

    /**
     * Method used to get the media url.
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}
