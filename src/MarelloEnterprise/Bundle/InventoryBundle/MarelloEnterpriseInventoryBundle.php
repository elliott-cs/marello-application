<?php

namespace MarelloEnterprise\Bundle\InventoryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MarelloEnterpriseInventoryBundle extends Bundle
{
    public function getParent()
    {
        return 'MarelloInventoryBundle';
    }
}
