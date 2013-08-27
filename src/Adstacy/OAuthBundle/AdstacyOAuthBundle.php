<?php

namespace Adstacy\OAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdstacyOAuthBundle extends Bundle
{
    public function getParent()
    {
        return 'HWIOAuthBundle';
    }
}
