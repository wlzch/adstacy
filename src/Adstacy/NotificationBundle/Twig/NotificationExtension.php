<?php

namespace Adstacy\NotificationBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Adstacy\NotificationBundle\Entity\Notification;

class NotificationExtension extends \Twig_Extension
{
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'render_top_notification' => new \Twig_Function_Method($this, 'renderTopNotification', array('is_safe' => array('html'))),
            'render_all_notification' => new \Twig_Function_Method($this, 'renderAllNotification', array('is_safe' => array('html'))),
        );
    }

    /**
     * Render top notifications
     *
     * @param integer $limit
     */
    public function renderTopNotification($limit = 5)
    {
        $notifications = $this->getNotifications($limit, false);

        return $this->container->get('templating')->render(
            'AdstacyNotificationBundle::top_notifications.html.twig', array(
                'notifications' => $notifications
            )
        );
    }

    /**
     * Render all notifications
     */
    public function renderAllNotification()
    {
        $notifications = $this->getNotifications($this->container->getParameter('max_notifications'));

        return $this->container->get('templating')->render(
            'AdstacyNotificationBundle::all_notifications.html.twig', array(
                'notifications' => $notifications,
            )
        );
    }

    /**
     * Get notifications
     *
     * @param integer $limit
     * @param false|null get all/only unread notifications
     *
     * @return array
     */
    private function getNotifications($limit, $read = null)
    {
        $repo = $this->container->get('doctrine')->getRepository('AdstacyNotificationBundle:Notification');
        $user = $this->container->get('security.context')->getToken()->getUser();
        $notifications = array();
        if ($user->getNotificationsCount() > 0) {
            $query = $repo->findNotificationsForUserQuery($user, $read);
            $adapter = new DoctrineORMAdapter($query);
            $paginator = new Pagerfanta($adapter);
            $paginator->setMaxPerPage($limit);
            $paginator->setCurrentPage(1);

            $notificationManager = $this->container->get('adstacy.notification.manager');
            foreach ($paginator->getCurrentPageResults() as $notification) {
                $notification = array(
                    'user' => $notification->getFrom(),
                    'content' => $notificationManager->format($notification),
                    'created' => $notification->getCreated()
                );
                $notifications[] = $notification;
            }
        }

        return $notifications;
    }

    public function getName()
    {
        return 'adstacy_notification_extension';
    }
}
