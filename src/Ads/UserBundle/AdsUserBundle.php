<?php

namespace Ads\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdsUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
