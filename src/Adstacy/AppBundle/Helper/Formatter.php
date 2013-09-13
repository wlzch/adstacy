<?php

namespace Adstacy\AppBundle\Helper;

class Formatter
{
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
}
