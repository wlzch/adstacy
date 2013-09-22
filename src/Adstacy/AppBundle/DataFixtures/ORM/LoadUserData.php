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
        $suwandi->setRealname('Suwandi Halim');
        $suwandi->setEmail('wandi.lin13@gmail.com');
        $suwandi->setAbout('IU fans\' items online seller, #IU \'s #album, #poster, #fashion and many more stuffs. Location for #Medan, #Indonesia. YM: wandi.lin@yahoo.com Hangout: wandi.lin13@gmail.com BB Pin: 25fa9088 HP: 0877 9399 5355');
        $encoder = $this->get('security.encoder_factory')->getEncoder($suwandi);
        $password = $encoder->encodePassword('suwandi', $suwandi->getSalt());
        $suwandi->setPassword($password);

        $welly = new User();
        $welly->setUsername('welly');
        $welly->setRealname('Welly Huang');
        $welly->setAbout('IU fans\' items online seller, #IU \'s #album, #poster, #fashion and many more stuffs. Location for #Medan, #Indonesia. YM: wandi.lin@yahoo.com Hangout: wandi.lin13@gmail.com BB Pin: 25fa9088 HP: 0877 9399 5355');
        $welly->setEmail('wilzichi92@gmail.com');
        $encoder = $this->get('security.encoder_factory')->getEncoder($welly);
        $password = $encoder->encodePassword('welly', $welly->getSalt());
        $welly->setPassword($password);

        $erwin = new User();
        $erwin->setUsername('rwinz');
        $erwin->setRealname('Erwin Zhang');
        $erwin->setEmail('rw7nz.cyruz@gmail.com');
        $erwin->setAbout('IU fans\' items online seller, #IU \'s #album, #poster, #fashion and many more stuffs. Location for #Medan, #Indonesia. YM: wandi.lin@yahoo.com Hangout: wandi.lin13@gmail.com BB Pin: 25fa9088 HP: 0877 9399 5355');
        #$erwin->setAbout($this->faker->sentence(10));
        $encoder = $this->get('security.encoder_factory')->getEncoder($erwin);
        $password = $encoder->encodePassword('rwinz', $erwin->getSalt());
        $erwin->setPassword($password);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setRealname('Admin');
        $admin->setEmail('admin@termedan.com');
        $admin->addRole('ROLE_SUPER_ADMIN');
        $encoder = $this->get('security.encoder_factory')->getEncoder($admin);
        $password = $encoder->encodePassword('admin', $admin->getSalt());
        $admin->setPassword($password);

        $manager->persist($suwandi);
        $manager->persist($welly);
        $manager->persist($erwin);
        $manager->persist($admin);

        $this->addReference('user-suwandi', $suwandi);
        $this->addReference('user-welly', $welly);
        $this->addReference('user-erwin', $erwin);
        $this->addReference('user-admin', $admin);

        $users = array('andy', 'ricky', 'robert', 'wilson', 'dennis', 'hendra', 'david', 'rudy',
            'tony', 'jimmy', 'rita', 'fanny', 'dewi', 'kartika', 'angela', 'yenny', 'lisa', 'jenny',
            'erlika', 'tina', 'louis', 'sally', 'chistine', 'beny', 'yurica', 'melisa', 'anita',
            'wendy', 'susanti', 'albert', 'hendy', 'fendy', 'stanley', 'siska', 'cindy', 'catherine',
            'antonio', 'steven', 'novita', 'cynthia', 'andika', 'putra', 'putri', 'lusi', 'linda',
            'sanny', 'erna', 'halim', 'rani', 'dicky', 'july', 'donita', 'mega', 'mentari', 'mika');
        foreach ($users as $user) {
            $tmp = new User();
            $tmp->setUsername($user);
            $tmp->setRealname($user);
            $tmp->setEmail($user.'@gmail.com');
            $tmp->setAbout('IU fans\' items online seller, #IU \'s #album, #poster, #fashion and many more stuffs. Location for #Medan, #Indonesia. YM: wandi.lin@yahoo.com Hangout: wandi.lin13@gmail.com BB Pin: 25fa9088 HP: 0877 9399 5355');
            $encoder = $this->get('security.encoder_factory')->getEncoder($tmp);
            $password = $encoder->encodePassword($user, $tmp->getSalt());
            $tmp->setPassword($password);

            $manager->persist($tmp);
            $this->addReference('user-'.$user, $tmp);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
