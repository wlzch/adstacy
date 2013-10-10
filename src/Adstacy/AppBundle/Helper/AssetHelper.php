<?php

namespace Adstacy\AppBundle\Helper;

use Symfony\Component\Routing\RouterInterface;

class AssetHelper
{
    protected $router;

    /**
     * Receive Router instance
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Implement asset_url function
     * We get the router context. This will default to settings in
     * parameters.yml if there is no active request
     */
    public function assetUrl($path)
    {
        $context = $this->router->getContext();
        $host = $context->getScheme().'://'.$context->getHost();

        return $host.$path;
    }
}
