<?php
declare(strict_types=1);

namespace Wendover\Catalog\Block\Product\ProductList;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class Toolbar
 * @package Wendover\Catalog\Block\Product\ProductList
 */
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var HttResponse
     */
    protected $response;

    /**
     * @var ToolbarMemorizer
     */
    private $toolbarMemorizer;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    /**
     * Default direction
     *
     * @var string
     */
    protected $_direction = 'asc';

    /**
     * Toolbar constructor.
     * @param ProductList $productListHelper
     * @param ToolbarMemorizer|null $toolbarMemorizer
     * @param \Magento\Framework\App\Http\Context|null $httpContext
     * @param \Magento\Framework\Data\Form\FormKey|null $formKey
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Config $catalogConfig,
        ToolbarModel $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        ProductList $productListHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        private readonly CollectionFactory $productCollectionFactory,
        private readonly RequestInterface $request,
        array $data = [],
        ToolbarMemorizer $toolbarMemorizer = null,
        \Magento\Framework\App\Http\Context $httpContext = null,
        \Magento\Framework\Data\Form\FormKey $formKey = null
    ) {
        parent::__construct(
            $context,
            $catalogSession,
            $catalogConfig,
            $toolbarModel,
            $urlEncoder,
            $productListHelper,
            $postDataHelper,
            $data,
            $toolbarMemorizer,
            $httpContext,
            $formKey
        );
        $this->toolbarMemorizer = $toolbarMemorizer ?: ObjectManager::getInstance()->get(
            ToolbarMemorizer::class
        );
        $this->formKey = $formKey ?: ObjectManager::getInstance()->get(
            \Magento\Framework\Data\Form\FormKey::class
        );
    }

    /**
     * Total number of products in current category.
     *
     * @return int
     */
    public function getTotalNum()
    {
        $params = $this->request->getParams();
        if (!empty($params) && !empty($params['searchSku'])) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('sku', array('like' => '%'.$params['searchSku'].'%'));
            $collection->addCategoriesFilter(['in' => $params['id']]);
            $resultCount = $collection->count();

            if (is_object($collection)) {
                return $resultCount;
            }
        }

        return parent::getTotalNum();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWidgetOptionsJson(array $customOptions = []): false|string
    {
        $defaultMode = $this->_productListHelper->getDefaultViewMode($this->getModes());
        $options = [
            'mode' => ToolbarModel::MODE_PARAM_NAME,
            'direction' => ToolbarModel::DIRECTION_PARAM_NAME,
            'order' => ToolbarModel::ORDER_PARAM_NAME,
            'limit' => ToolbarModel::LIMIT_PARAM_NAME,
            'modeDefault' => $defaultMode,
            'directionDefault' => $this->_direction ?: ProductList::DEFAULT_SORT_DIRECTION,
            'orderDefault' => $this->getOrderField(),
            'limitDefault' => $this->_productListHelper->getDefaultLimitPerPageValue($defaultMode),
            'url' => $this->getPagerUrl(),
            'formKey' => $this->formKey->getFormKey(),
            'post' => $this->toolbarMemorizer->isMemorizingAllowed() ? true : false
        ];
        $options = array_replace_recursive($options, $customOptions);

        return json_encode(['productListToolbarForm' => $options], JSON_THROW_ON_ERROR);
    }

    /**
     * Return products collection instance
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        $params = $this->request->getParams();
        if (!empty($params) && !empty($params['searchSku'])) {
            $pagePerItems = isset($params['product_list_limit']) ? $params['product_list_limit'] : 12;
            $page = isset($params['p']) ? $params['p'] : 1;
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('sku', array('like' => '%'.$params['searchSku'].'%'));
            $collection->addCategoriesFilter(['in' => $params['id']]);
            $collection->setPageSize($pagePerItems);
            $collection->setCurPage($page);

            if (is_object($collection)) {
                return $collection;
            }
        }

        return parent::getCollection();
    }
}
