<?php
namespace Wendover\ReportImages\Ui\Component\Listing\Columns;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class ProductActions extends Column
{
    private $urlBuilder;
    const PRODUCT_URL_PATH_EDIT = 'catalog/product/edit';
    protected $_ProductRepository;
    protected $uiComponentFactory;
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $ProductRepository,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [])
    {
        $this->_ProductRepository = $ProductRepository;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components,
            $data);
    }
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'catalog/product/edit',
                        ['id' => $item['product_id']]
                    ),
                    'ariaLabel' => __('Edit ') . $item['name'],
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
