<?php

namespace Adstacy\NotificationBundle\NotificationSaver;

use Adstacy\AppBundle\Entity\User;

interface NotificationSaverInterface
{
    public function save(User $from, User $to, $noun, $flush = false);
    public function support($noun);
}
