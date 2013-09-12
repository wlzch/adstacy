<?php

namespace Adstacy\NotificationBundle\NotificationSaver;

use Adstacy\AppBundle\Entity\User;

interface NotificationSaverInterface
{
    /**
     * Save notification
     *
     * @param User $from
     * @param User $to
     * @param mixed $nount
     * @param boolean $flush
     */
    public function save(User $from, User $to, $noun, $flush = false);

    /**
     * Checks wheter this notification saver can save $noun
     *
     * @param mixed $noun
     * @param mixed anything to identify what notification to call
     *
     * @return boolean
     */
    public function support($noun, $key);
}
