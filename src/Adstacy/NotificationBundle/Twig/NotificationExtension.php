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
            'render_notification' => new \Twig_Function_Method($this, 'renderNotification', array('is_safe' => array('html'))),
        );
    }

    /**
     * Render login form
     *
     * @param integer $limit
     * @param string css id
     */
    public function renderNotification($limit, $id)
    {
        $repo = $this->container->get('doctrine')->getRepository('AdstacyNotificationBundle:Notification');
        $user = $this->container->get('security.context')->getToken()->getUser();
        $query = $repo->findNotificationsForUserQuery($user);
        $adapter = new DoctrineORMAdapter($query);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage(1);

        $notificationManager = $this->container->get('adstacy.notification.manager');
        $notifications = array();
        foreach ($paginator->getCurrentPageResults() as $notification) {
            $notifications[] = $notificationManager->format($notification);
        }

        return $this->container->get('templating')->render('AdstacyNotificationBundle::notifications.html.twig', array(
            'notifications' => $notifications,
            'id' => $id
        ));
    }

    public function getName()
    {
        return 'adstacy_notification_extension';
    }
}
