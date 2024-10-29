<?php
declare(strict_types=1);

namespace Perficient\ViewInRoom\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perficient\ViewInRoom\Helper\Data;

class ViewInRoomViewModel implements ArgumentInterface
{
    public function __construct(
        private readonly Data $viewInRoomHelper
    )
    {}

    public function isLoggedInCustomer()
    {
        return $this->viewInRoomHelper->isLoggedInCustomer();
    }

    public function isProductCustomizer($_product)
    {
        return $this->viewInRoomHelper->isProductCustomizer($_product);
    }

    public function getJsonConfig($_product)
    {
        return $this->viewInRoomHelper->getJsonConfig($_product);
    }
}
