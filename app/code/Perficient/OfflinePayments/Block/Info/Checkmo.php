<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Perficient\OfflinePayments\Block\Info;

class Checkmo extends \Magento\OfflinePayments\Block\Info\Checkmo
{

    /**
     * @var string
     */
    protected $_template = 'Perficient_OfflinePayments::info/checkmo.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Perficient_OfflinePayments::info/pdf/checkmo.phtml');
        return $this->toHtml();
    }
}
