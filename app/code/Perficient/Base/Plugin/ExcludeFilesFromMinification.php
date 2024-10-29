<?php

namespace Perficient\Base\Plugin;
use Magento\Framework\View\Asset\Minification;

class ExcludeFilesFromMinification
{
    public function aroundGetExcludes(Minification $subject, callable $proceed, $contentType)
    {
        $result = $proceed($contentType);
        if ($contentType != 'js') {
            return $result;
        }
        $result[] = 'Perficient_Checkout/js/add-collection';
        $result[] = 'Magento_Company/js/user-edit';
        $result[] = 'Perficient_Company/requirejs-config.js';
        $result[] = 'Perficient_Company/js/user-edit-mixin';
        $result[] = 'Perficient_Company/js/company_registration';
        $result[] = 'Perficient_Company/js/restrict_addtocart';
        $result[] = 'Perficient_WishlistSet/js/addtowishlistcollection';
        return $result;
    }
}