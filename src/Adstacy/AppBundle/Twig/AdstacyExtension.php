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
            'sort_comments' => new \Twig_Function_Method($this, 'sortComments'),
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

    /**
     * Sort comments
     *
     * @param array $comments
     *
     * @return array
     */
    public function sortComments($comments = array())
    {
        if (!is_array($comments)) {
          $comments = $comments->toArray();
        }
        usort($comments, function($a, $b) {
          return $a->getId() > $b->getId();
        });

        return $comments;
    }

    public function getName()
    {
        return 'adstacy_extension';
    }
}

