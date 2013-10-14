<?php

namespace Adstacy\AppBundle\Helper;

use Symfony\Component\HttpFoundation\File\File;
use Imagine\Gd\Imagine;
use Imagine\Filter\Advanced\RelativeResize;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

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

    /**
     * Download image from public accessible url and return a File object from it
     *
     * @param string url
     *
     * @return File
     */
    static public function downloadImage($url)
    {
        try {
            $content = file_get_contents($url);
        } catch (\Exception $e) {
            return false;
        }
        if (!$content) return false;

        $path = '/tmp/'.uniqid();
        file_put_contents($path, $content);
        $ext = self::guessExtension($path);
        $newpath = $path.'.'.$ext;
        rename($path, $newpath);
        $file = new File($newpath);

        return $file;
    }

    static public function guessExtension($path)
    {
        $mimeTypeGuesser = MimeTypeGuesser::getInstance();
        $extensionGuesser = ExtensionGuesser::getInstance();

        $type = $mimeTypeGuesser->guess($path);
        $guesser = ExtensionGuesser::getInstance();

        return $guesser->guess($type);
    }
}
