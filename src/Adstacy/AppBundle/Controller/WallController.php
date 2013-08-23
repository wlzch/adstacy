<?php

namespace Adstacy\AppBundle\Controller;

use Adstacy\AppBundle\Entity\Wall;
use Adstacy\AppBundle\Form\Type\WallType;
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

        return $this->render('AdstacyAppBundle:Wall:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
