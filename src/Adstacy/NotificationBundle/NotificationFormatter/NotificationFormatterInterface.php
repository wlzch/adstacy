<?php

namespace Adstacy\NotificationBundle\NotificationFormatter;

use Adstacy\NotificationBundle\Entity\Notification;

interface NotificationFormatterInterface
{
    public function format(Notification $notification);
    public function support(Notification $notification);
}
