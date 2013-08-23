<?php

namespace Adstacy\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdstacyUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
