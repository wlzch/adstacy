<?php

namespace Adstacy\AppBundle\Twig;

use Adstacy\AppBundle\Helper\AssetHelper;

class AssetExtension extends \Twig_Extension
{
    protected $helper;

    /**
     * Receive Router instance
     */
    public function __construct(AssetHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Declare the asset_url function
     */
    public function getFunctions()
    {
        return array(
            'asset_url' => new \Twig_Function_Method($this, 'assetUrl'),
        );
    }

    /**
     * Implement asset_url function
     * We get the router context. This will default to settings in
     * parameters.yml if there is no active request
     */
    public function assetUrl($path)
    {
        return $helper->assetUrl($path);
    }

    /**
     * Set a name for the extension
     */
    public function getName()
    {
        return 'adstacy_asset_extension';
    }
}
