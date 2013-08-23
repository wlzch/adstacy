<?php

namespace Adstacy\AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class HashtagToArrayTransformer implements DataTransformerInterface
{
    /**
     * Transform $tags array to hashtag text
     *
     * @param array $tags
     *
     * @return string hashtags
     */
    public function transform($tags = array())
    {
        $_tags = array();
        foreach ($tags as $tag) {
            $_tags[] = '#'.$tag;
        }

        return implode(' ', $_tags); 
    }

    /**
     * Transform hashtag text to $tags array
     *
     * @param string $hastags
     *
     * @return array $tags
     */
    public function reverseTransform($hashtags)
    {
        $matches = null;
        preg_match_all('/#(\w+)/', $hashtags, $matches);

        return $matches[1];
    }
}
