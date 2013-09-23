<?php

namespace Adstacy\NotificationBundle\NotificationFormatter;

use Adstacy\NotificationBundle\Entity\Notification;

interface NotificationFormatterInterface
{
    public function getImage(Notification $notification, $absolute = false);
    public function getTime(Notification $notification);
    public function getName(Notification $notification);
    public function getUrl(Notification $notification, $absolute = false);
    public function getText(Notification $notification);
    public function support(Notification $notification);
}
