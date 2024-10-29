<?php
/**
 * Created by PhpStorm.
 * User: sandeep.mude
 * Date: 01-10-2020
 * Time: 09:31 PM
 */

namespace Perficient\PriceMultiplier\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Perficient\Company\Helper\Data as CompanyHelper;

/**
 * Class PriceMultiplier
 * @package Perficient\PriceMultiplier\Ui\Component\Listing\Column
 */
class PriceMultiplier extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * PriceMultiplier constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected readonly CompanyHelper $companyHelper,
        array $components = [],
        array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (empty($item[$fieldName])) {
                    $item[$fieldName] = '';
                }
            }
        }
        return $dataSource;
    }

    /**
     * remove multiplier based on customer type from ui component grid
     */
    public function prepare(): void
    {
        parent::prepare();
        $isB2cCustomer = $this->companyHelper->isB2cCustomer();
        if($isB2cCustomer) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }

}
