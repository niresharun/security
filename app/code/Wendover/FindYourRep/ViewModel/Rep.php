<?php
namespace Wendover\FindYourRep\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context;

class Rep implements ArgumentInterface
{
    public function __construct(
        protected HttpContext $httpContext
    ) {
    }

    public function isCustomerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
