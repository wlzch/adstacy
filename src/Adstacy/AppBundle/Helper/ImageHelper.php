<?php

namespace Adstacy\AppBundle\Helper;

use Symfony\Component\HttpFoundation\File\File;
use Imagine\Gd\Imagine;
use Imagine\Filter\Advanced\RelativeResize;

class ImageHelper
{
    static public function resizeImage(File $image, $width = 1024)
    {
        $originalImage = $image;
        $imagine = new Imagine();
        $image = $imagine->open($image);
        $size = $image->getSize();
        if ($size->getWidth() > 0 && $size->getHeight() > 0) {
            if ($size->getWidth() > $width) {
                $relativeResize = new RelativeResize('widen', $width);
                $image = $relativeResize->apply($image);
                $image->save($originalImage->getRealPath(), array(
                        'format' => $originalImage->guessClientExtension()
                    )
                );
            }
        }

        return $originalImage;
    }
}
