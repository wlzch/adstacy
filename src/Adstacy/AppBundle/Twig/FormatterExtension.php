<?php

namespace Adstacy\AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig extension to format
 */
class FormatterExtension extends \Twig_Extension
{
    private $container;
    private $router;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->router = $this->container->get('router');
    }

    public function getFunctions()
    {
        return array(
            'parse_hashtag' => new \Twig_Function_Method($this, 'parseHashtag', array('is_safe' => array('html'))),
            'more' => new \Twig_Function_Method($this, 'more', array('is_safe' => array('html'))),
        );
    }

    /**
     * Read more
     * @link http://stackoverflow.com/questions/4258557/limit-text-length-in-php-and-provide-read-more-link
     *
     * @param string text
     * @param integer length
     *
     * @return string truncated text
     */
    public function more($text, $len)
    {
        $text = strip_tags($text);

        if (strlen($text) > $len) {

            // truncate string
            $stringCut = substr($text, 0, $len);

            // make sure it ends in a word so assassinate doesn't become ass...
            $text = substr($stringCut, 0, strrpos($stringCut, ' ')).'...';
        }

        return $text;
    }

    /**
     * Parse hashbang and add link to it
     *
     * @param string text
     *
     * @return string parsed text
     */
    public function parseHashtag($text)
    {
        $url = $this->router->generate('adstacy_app_search');
        return preg_replace_callback('/(#\w+)/', function($matches) use (&$url) {
            return sprintf(' <a href="%s?q=%s">%s</a>', $url, substr($matches[0], 1), $matches[0]);
        }, $text);
    }


    public function getName()
    {
        return 'adstacy_formatter_extension';
    }
}

