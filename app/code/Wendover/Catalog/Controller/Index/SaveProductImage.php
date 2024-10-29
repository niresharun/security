<?php

namespace Wendover\Catalog\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Wendover\Catalog\Controller\Exception;

class SaveProductImage implements ActionInterface
{
    private $resultJson;
    const MEDIA_FOLDER_PATH = 'tearsheet/savedcanvas/';
    const DIRECTORY_SEPARATOR = '/';

    public function __construct(
        private readonly JsonFactory           $jsonResultFactory,
        private readonly RequestInterface      $request,
        private readonly Filesystem            $filesystem,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function execute()
    {
        $responseJson = 0;
        try {
            $data = $this->request->getParam('imgData');
            $sku  = $this->request->getParam('sku');
            $productArtworkDataJson = $this->request->getParam('artworkData');
            $productArtworkData = json_decode($productArtworkDataJson,true);
            $pzSelectedOptions = $productArtworkData['pzSelectedOptions'];
            if(isset($pzSelectedOptions['frame']) && $pzSelectedOptions['frame']['sku'])   {
                $designedImageNameParams['frame'] = $pzSelectedOptions['frame']['sku'];
                $responseJson =1;
            }
            $img = str_replace('data:image/jpeg;base64,', '', $data);
            $uploadImg = $this->storeManager->getStore()->getBaseMediaDir() . self::DIRECTORY_SEPARATOR . self::MEDIA_FOLDER_PATH;
            if (!file_exists($uploadImg)) {
                mkdir($uploadImg, 0777, true);
            }
            $uploadImg.= $sku.'.jpeg';
            if (file_exists($uploadImg)) {
                unlink($uploadImg);
            }
            file_put_contents($uploadImg, base64_decode($img));
            $result = array(
                'response' => $responseJson
            );
            $resultJson = $this->jsonResultFactory->create();
            $resultJson->setData($result);
            return $resultJson;
        } catch (Exception $e) {
            return false;
        }
    }
}
