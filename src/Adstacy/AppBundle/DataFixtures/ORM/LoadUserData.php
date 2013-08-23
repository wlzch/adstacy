<?php

namespace Adstacy\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Adstacy\AppBundle\Entity\User;

class LoadUserData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $suwandi = new User();
        $suwandi->setUsername('suwandi');
        $suwandi->setEmail('wandi.lin13@gmail.com');
        $encoder = $this->get('security.encoder_factory')->getEncoder($suwandi);
        $password = $encoder->encodePassword('suwandi', $suwandi->getSalt());
        $suwandi->setPassword($password);

        $welly = new User();
        $welly->setUsername('welly');
        $welly->setEmail('wilzichi92@gmail.com');
        $encoder = $this->get('security.encoder_factory')->getEncoder($welly);
        $password = $encoder->encodePassword('welly', $welly->getSalt());
        $welly->setPassword($password);

        $erwin = new User();
        $erwin->setUsername('rwinz');
        $erwin->setEmail('rwinz.cyruz@gmail.com');
        $encoder = $this->get('security.encoder_factory')->getEncoder($erwin);
        $password = $encoder->encodePassword('erwin', $erwin->getSalt());
        $erwin->setPassword($password);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@termedan.com');
        $admin->addRole('ROLE_SUPER_ADMIN');
        $encoder = $this->get('security.encoder_factory')->getEncoder($admin);
        $password = $encoder->encodePassword('admin', $admin->getSalt());
        $admin->setPassword($password);

        $manager->persist($suwandi);
        $manager->persist($welly);
        $manager->persist($erwin);
        $manager->persist($admin);
        $manager->flush();

        $this->addReference('user-suwandi', $suwandi);
        $this->addReference('user-welly', $welly);
        $this->addReference('user-erwin', $erwin);
        $this->addReference('user-admin', $admin);
    }

    public function getOrder()
    {
        return 1;
    }
}
