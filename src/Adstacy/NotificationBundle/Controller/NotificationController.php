<?php

namespace Adstacy\NotificationBundle\Controller;

use Adstacy\AppBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Show notifications
     */
    public function showNotificationsAction()
    {
        $this->readNotifications();
        return $this->render('AdstacyNotificationBundle::show_notifications.html.twig');
    }

    /**
     * @Secure(roles="ROLE_USER")
     */
    public function readAllAction()
    {
        $this->readNotifications();
        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonResponse(json_encode(array('status' => 'success')));
        }

        $this->addFlash('success', $this->translate('notification.read'));
        
        return $this->redirect($this->generateUrl('adstacy_app_notifications'));
    }

    private function readNotifications()
    {
        $em = $this->getManager();
        $user = $this->getUser();
        if ($user->getNotificationsCount() > 0) {
            $user->setNotificationsCount(0);
            $query = $em->createQuery('
                UPDATE
                AdstacyNotificationBundle:Notification n
                SET n.read = TRUE
                WHERE n.from = :user
            ');
            $query->setParameter('user', $user);
            $query->execute();
            $em->persist($user);
            $em->flush();
        }
    }
}
