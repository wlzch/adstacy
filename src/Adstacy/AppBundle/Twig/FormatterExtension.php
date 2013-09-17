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
            'remove_hashtag' => new \Twig_Function_Method($this, 'removeHashtag', array('is_safe' => array('html'))),
            'parse_url' => new \Twig_Function_Method($this, 'parseUrl', array('is_safe' => array('html'))),
            'parse_mention' => new \Twig_Function_Method($this, 'parseMention', array('is_safe' => array('html'))),
            'more' => new \Twig_Function_Method($this, 'more', array('is_safe' => array('html'))),
            'ago' => new \Twig_Function_Method($this, 'ago', array('is_safe' => array('html'))),
        );
    }

    /**
     * Read more
     *
     * @param string text
     * @param integer length
     *
     * @return string truncated text
     */
    public function more($text, $len)
    {
        return $this->container->get('adstacy.helper.formatter')->more($text, $len);
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

    /**
     * Remove all hashtags from text
     *
     * @param string text
     *
     * @return string
     */
    public function removeHashtag($text)
    {
        return preg_replace('/(#\w+)/', '', $text);
    }

    /**
     * Parse url and add link to it
     *
     * @param string text
     *
     * @return string parsed text
     */
    public function parseUrl($text)
    {
        return preg_replace_callback(
          "#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#i",
          function($matches) {
              return sprintf('<a class="url" href="%s" target="_blank">%s</a>', $matches[1], $matches[3]);
          }, $text
        );
    }

    /**
     * Parse mention and add link to it
     *
     * @param string text
     *
     * @return string parsed text
     */
    public function parseMention($text)
    {
        $router = $this->router;
        return preg_replace_callback('/@([^@ ]+)/', function($matches) use (&$router) {
            $username = substr($matches[0], 1);
            $url = $router->generate('adstacy_app_user_profile', array('username' => $username));
            return sprintf('<a class="mention" href="%s">%s</a>', $url, $matches[0]);
        }, $text);
    }

    /**
     * @link http://css-tricks.com/snippets/php/time-ago-function/
     * Return time in ago format
     *
     * @param Datetime
     *
     * @return string formated
     */
    public function ago(\DateTime $date)
    {
        return $this->container->get('adstacy.helper.formatter')->ago($date);
    }


    public function getName()
    {
        return 'adstacy_formatter_extension';
    }
}

