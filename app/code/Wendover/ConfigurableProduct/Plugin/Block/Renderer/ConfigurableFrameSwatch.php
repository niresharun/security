<?php
declare(strict_types=1);

namespace Wendover\ConfigurableProduct\Plugin\Block\Renderer;

use Magento\Swatches\Block\Product\Renderer\Configurable;

class ConfigurableFrameSwatch
{
    protected $defaultTemplate = 'Magento_ConfigurableProduct::product/view/type/options/configurable.phtml';
    protected $customTemplate = 'Wendover_ConfigurableProduct::type/options/configurable.phtml';
    public function beforeSetTemplate(Configurable $subject, string $result): string {
        if ($result === $this->defaultTemplate) {
            return $this->customTemplate;
        }
        return $result;
    }
}
