<?php

namespace Adstacy\AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 
 */
class AdstacyExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'is_ajax' => new \Twig_Function_Method($this, 'isAjax'),
            'is_mobile' => new \Twig_Function_Method($this, 'isMobile'),
            'render_sidebar_trending' => new \Twig_Function_Method($this, 'renderSidebarTrending', array(
                'is_safe' => array('html')
            )),
        );
    }

    public function isMobile()
    {
        return $this->container->get('adstacy.mobile_detect')->isMobile();
    }
    
    public function isAjax()
    {
        return $this->container->get('request')->isXmlHttpRequest();
    }

    public function renderSidebarTrending()
    {
        $redis = $this->container->get('snc_redis.default');
        $ids = $redis->lrange('trending', 0, -1);
        $id = $ids[rand(0, count($ids) - 1)];
        $ad = $this->container->get('doctrine')->getManager()->getRepository('AdstacyAppBundle:Ad')->findOneById($id);

        return $this->container->get('templating')->render(
            'AdstacyAppBundle:App:sidebar_trending.html.twig', array(
                'ad' => $ad
            )
        );
    }

    public function getName()
    {
        return 'adstacy_extension';
    }
}

