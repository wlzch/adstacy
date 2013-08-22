<?php

namespace Ads\AppBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Ads\AppBundle\Entity\Post;

class PostManager
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Set height on post
     *
     * @param array|Post array of posts or Post
     */
    public function setHeight($posts, $filter = 'thumbnail')
    {
        if ($posts instanceof Post) {
            $posts = array($posts);
        }
        $storage = $this->container->get('vich_uploader.storage');
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $dataManager = $this->container->get('liip_imagine.data.manager');
        $filterManager = $this->container->get('liip_imagine.filter.manager');
        $request = $this->container->get('request');
        $em = $this->container->get('doctrine')->getManager();
        foreach ($posts as $post) {
          if ($post instanceof Post && $image = $post->getImage()) {

              $origPath = $storage->resolveUri($image, 'file'); // resolves real file uri (vich)
              $this->container->get('liip_imagine.controller')->filterAction($request, $origPath, $filter); // store the file

              $targetPath = str_replace($request->getBaseUrl(), '', $cacheManager->generateUrl($origPath, $filter)); // get thumbnail file path
              $size = $dataManager->find($filter, $targetPath)->getSize();
              $post->setThumbHeight($size->getHeight());
              $em->persist($post);
          }
        }
        $em->flush();
    }
}
