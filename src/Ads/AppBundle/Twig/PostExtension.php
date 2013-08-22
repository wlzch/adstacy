<?php

namespace Ads\AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Ads\AppBundle\Entity\Post;

/**
 * Twig extension 
 */
class PostExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'post_thumbnail_size' => new \Twig_Function_Method($this, 'getPostThumbnailSize', array('is_safe' => array('html'))),
        );
    }

    /**
     * Get post thumbnail size. Post height will be set if it doesnt have any yet.
     *
     * @param Post $post
     *
     * @return array [width, height]
     */
    public function getPostThumbnailSize(Post $post)
    {
        if ($post->getThumbHeight()) {
            $filterSets = $this->container->getParameter('liip_imagine.filter_sets');
            $width = $filterSets['thumbnail']['filters']['relative_resize']['widen'];

            return [$width, $post->getThumbHeight()];
        }

        $request = $this->container->get('request');
        $storage = $this->container->get('vich_uploader.storage');
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $dataManager = $this->container->get('liip_imagine.data.manager');
        $em = $this->container->get('doctrine')->getManager();

        $image = $post->getImage();
        $uri = $storage->resolveUri($image, 'file');
        $path = str_replace($request->getBaseUrl(), '', $cacheManager->generateUrl($uri, 'thumbnail'));

        $dataManager = $this->container->get('liip_imagine.data.manager');
        $size = $dataManager->find('thumbnail', $path)->getSize();
        $post->setThumbHeight($size->getHeight());
        $em->persist($post);
        $em->flush();

        return [$size->getWidth(), $size->getHeight()];
    }


    public function getName()
    {
        return 'ads_image_extension';
    }
}

