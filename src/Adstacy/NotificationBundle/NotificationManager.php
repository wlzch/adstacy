<?php

namespace Adstacy\NotificationBundle;

use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Entity\Comment;
use Adstacy\NotificationBundle\Entity\Notification;
use Adstacy\NotificationBundle\NotificationSaver\NotificationSaverInterface;
use Adstacy\NotificationBundle\NotificationFormatter\NotificationFormatterInterface;

class NotificationManager
{
    private $savers;
    private $formatters;

    public function __construct($savers = array(), $formatters = array())
    {
        foreach ($savers as $saver) {
            if (!$saver instanceof NotificationSaverInterface) {
                throw \Exception('Must implement NotificationSaverInterface');
            }
        }
        foreach ($formatters as $formatter) {
            if (!$formatter instanceof NotificationFormatterInterface) {
                throw \Exception('Must implement NotificationFormatterInterface');
            }
        }
        $this->savers = $savers;
        $this->formatters = $formatters;
    }

    /**
     * Save notification
     *
     * @param User $from
     * @param User $to
     * @param mixed $noun
     * @param boolean $flush
     * @param mixed anything to identify what notification to call
     */
    public function save(User $from, User $to, $noun, $flush = false, $key = null)
    {
        // only notify if action is from two different users
        if ($from != $to) {
            foreach ($this->savers as $saver) {
                if ($saver->support($noun, $key)) {
                    $saver->save($from, $to, $noun, $flush);
                }
            };
        }
    }

    /**
     * Format notification
     *
     * @param Notification $notification
     *
     * @return string|false
     */
    public function format(Notification $notification)
    {
        foreach ($this->formatters as $formatter) {
            if ($formatter->support($notification)) {
                return $formatter->format($notification);
            }
        }

        return false;
    }
}
