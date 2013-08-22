<?php

namespace Ads\AppBundle\Controller;

use Ads\AppBundle\Entity\Post;
use Ads\AppBundle\Form\Type\PostType;
use Ads\AppBundle\Entity\Wall;
use Ads\AppBundle\Form\Type\WallType;
use JMS\SecurityExtraBundle\Annotation\Secure;

class PostController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $filter = 'thumbnail';
        $post = new Post();
        $form = $this->createForm(new PostType(), $post, array(
            'username' => $this->getUser()->getUsername() 
        ));
        $form->handleRequest($request);
        $wallForm = $this->createForm(new WallType(), new Wall(), array(
            'action' => $this->generateUrl('ads_app_wall_add') 
        ));

        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($post);
            $em->flush();

            $storage = $this->get('vich_uploader.storage');
            $cacheManager = $this->get('liip_imagine.cache.manager');
            $dataManager = $this->get('liip_imagine.data.manager');
            $filterManager = $this->get('liip_imagine.filter.manager');

            $image = $post->getImage();
            $origPath = $storage->resolveUri($image, 'file'); // resolves real file uri (vich)
            $this->get('liip_imagine.controller')->filterAction($request, $origPath, $filter); // store the file

            $targetPath = str_replace($request->getBaseUrl(), '', $cacheManager->generateUrl($origPath, $filter)); // get thumbnail file path
            $size = $dataManager->find($filter, $targetPath)->getSize();
            $post->setThumbHeight($size->getHeight());
            $em->persist($post);
            $em->flush();
        }

        return $this->render('AdsAppBundle:Post:add.html.twig', array(
            'form' => $form->createView(),
            'wallForm' => $wallForm->createView()
        ));
    }
}
