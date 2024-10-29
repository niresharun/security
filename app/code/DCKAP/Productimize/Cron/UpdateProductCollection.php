<?php

namespace DCKAP\Productimize\Cron;

use DCKAP\Productimize\Helper\CustomProductCollection;

class UpdateProductCollection
{
    protected $customProductCollection;

    public function __construct(CustomProductCollection $customProductCollection)
    {
        $this->customProductCollection = $customProductCollection;
    }

    public function execute()
    {
        $this->customProductCollection->getFrameCollection();
        $this->customProductCollection->getMatCollection();
    }
}
