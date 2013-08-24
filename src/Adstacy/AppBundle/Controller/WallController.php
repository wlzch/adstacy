<?php

namespace Adstacy\AppBundle\Controller;

use Adstacy\AppBundle\Entity\Wall;
use Adstacy\AppBundle\Form\Type\WallType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class WallController extends Controller
{

    /**
     * Show wall
     *
     * @param integer id
     */
    public function showAction($id)
    {
        $wall = $this->getRepository('AdstacyAppBundle:Wall')->find($id);

        if (!$wall) {
            throw $this->createNotFoundException();
        }

        return $this->render('AdstacyAppBundle:Wall:show.html.twig', array(
            'wall' => $wall
        ));
    }
    
    /**
     * @Secure(roles="ROLE_USER")
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $wall = new Wall();
        $wall->setUser($this->getUser()); // must be set here for uniqueness check
        $form = $this->createForm(new WallType(), $wall);
        $form->handleRequest($request);

        $serializer = $this->get('serializer');
        if ($form->isValid()) {
            $em = $this->getManager();
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
