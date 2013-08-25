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

        return $this->render('AdstacyAppBundle:Wall:create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Secure(roles="ROLE_USER")
     *
     * User B will be automatically followed by User A if it is not already
     */
    public function followAction($id)
    {
        $request = $this->getRequest();
        $user = $this->getUser();
        $repo = $this->getRepository('AdstacyAppBundle:Wall');
        $em = $this->getManager();

        $wall = $repo->find($id);
        if (!$wall) {
            throw $this->createNotFoundException();
        }

        // if user havent follow any wall from wall owner, user will automatically follow him
        $wallUser = $wall->getUser();

        if ($wallUser == $user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => 'You may not follow your own wall')));
            }
            $this->addFlash('error', 'You may not follow your own wall');
        } else {
            if (!$user->hasFollowedWall($wall)) {
                $wallsCount = $repo->countFollowedByUser($wallUser, $user);
                if ($wallsCount == 0) {
                    $wallUser->addFollower($user);
                }
                $wall->addFollower($user);

                $em->persist($wall);
                $em->persist($wallUser);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('id' => $wall->getId(), 'followers_count' => $wall->getFollowersCount())));
                }
                $this->addFlash('success', 'successfully followed this wall');
            } else {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('error' => 'You may not follow this wall yet')));
                }
                $this->addFlash('error', 'You may not follow this wall twice');
            }
        }

        return $this->redirect($this->generateUrl('adstacy_app_wall_show', array('id' => $id)));
    }

    /**
     * @Secure(roles="ROLE_USER")
     *
     * User B will be automatically unfollowed by User A if there is only this wall left
     */
    public function unfollowAction($id)
    {
        $request = $this->getRequest();
        $user = $this->getUser();
        $repo = $this->getRepository('AdstacyAppBundle:Wall');
        $em = $this->getManager();

        $wall = $repo->find($id);
        if (!$wall) {
            throw $this->createNotFoundException();
        }

        $wallUser = $wall->getUser();

        // user cannot unfollow his own wall
        if ($wallUser == $user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(json_encode(array('error' => 'You may not unfollow your own wall')));
            }
            $this->addFlash('error', 'You may not follow your own wall');
        } else {
            // user can only unfollow if he has follow it
            if ($user->hasFollowedWall($wall)) {
                // if this is the last wall $user follows $wallUser walls, $user will be automatically unfollow $wallUser
                $wallsCount = $repo->countFollowedByUser($wallUser, $user);
                if ($wallsCount == 1) {
                    $wallUser->removeFollower($user);
                }
                $wall->removeFollower($user);

                $em->persist($wall);
                $em->persist($wallUser);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('id' => $wall->getId(), 'followers_count' => $wall->getFollowersCount())));
                }
                $this->addFlash('success', 'successfully unfollowed this wall');
            } else {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(json_encode(array('error' => 'You may not unfollow this wall yet')));
                }
                $this->addFlash('error', 'You may not unfollow this wall twice');
            }
        }

        return $this->redirect($this->generateUrl('adstacy_app_wall_show', array('id' => $id)));
    }
}
