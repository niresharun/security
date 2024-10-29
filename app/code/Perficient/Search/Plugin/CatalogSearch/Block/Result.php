<?php
declare(strict_types=1);
/**
 * Plugin designed to replace `Amasty_Xsearch::result-count` plugin on Amasty_Xsearch module composer v1.22.1
 */
namespace Perficient\Search\Plugin\CatalogSearch\Block;

use Amasty\Xsearch\Helper\Data;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Response\Http;
use Perficient\Catalog\Helper\Data as CatalogHelper;

class Result
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Http
     */
    private $response;

    /**
     * @param Data $helper
     * @param Http $response
     * @param CatalogHelper $catalogHelper
     */
    public function __construct(
        Data $helper,
        Http $response,
        private readonly CatalogHelper $catalogHelper
    ) {
        $this->helper = $helper;
        $this->response = $response;
    }

    /**
     * @param $subject
     * @param int $result
     * @return int
     */
    public function afterGetResultCount($subject, $result)
    {
        if ($this->helper->isSingleProductRedirect()
            && !$subject->getRequest()->getParam('shopbyAjax')
            && $result == 1
        ) {
            /** @var ProductInterface $firstProduct */
            $firstProduct = $subject->getListBlock()->getLoadedProductCollection()->getFirstItem();
            $firstProductURL = $firstProduct->getProductUrl();

            if ($this->catalogHelper->getParentId((int) $firstProduct->getId())?->getTypeId() === Configurable::TYPE_CODE) {
                $firstProductURL = $this->catalogHelper->getMirrorProductUrl((int) $firstProduct->getId()) ?:
                    $firstProductURL;
            }
            $redirectUrl = $firstProductURL;
            $this->response->setRedirect($redirectUrl);
        }

        return $result;
    }
}
