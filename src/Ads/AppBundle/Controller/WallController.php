<?php

namespace Ads\AppBundle\Controller;

use Ads\AppBundle\Entity\Wall;
use Ads\AppBundle\Form\Type\WallType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class WallController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $wall = new Wall();
        $form = $this->createForm(new WallType(), $wall);
        $form->handleRequest($request);

        $serializer = $this->get('serializer');
        if ($form->isValid()) {
            $em = $this->getManager();
            $wall->setUser($this->getUser());
            $em->persist($wall);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse($serializer->serialize($wall, 'json'));
            }
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($serializer->serialize($form->getErrors(), 'json'));
        }

        return $this->render('AdsAppBundle:Wall:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
