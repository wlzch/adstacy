<?php

namespace Adstacy\NotificationBundle\NotificationFormatter;

use Adstacy\NotificationBundle\Entity\Notification;

interface NotificationFormatterInterface
{
    public function getImage(Notification $notification);
    public function getTime(Notification $notification);
    public function getName(Notification $notification);
    public function getUrl(Notification $notification);
    public function getText(Notification $notification);
    public function support(Notification $notification);
}
