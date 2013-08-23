<?php

namespace Adstacy\AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig extension to format
 */
class FormatterExtension extends \Twig_Extension
{
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
      return preg_replace('/(#\w+)/', ' <a href="#">$1</a>', $text);
    }


    public function getName()
    {
        return 'adstacy_formatter_extension';
    }
}

