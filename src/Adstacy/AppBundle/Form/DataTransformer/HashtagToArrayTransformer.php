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
        if ($tags && is_array($tags)) {
            return implode(',', $tags); 
        }

        return array();
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
        if ($hashtags) {
            return explode(',', $hashtags);
        }

        return array();
    }
}
