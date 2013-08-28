<?php

namespace Adstacy\AppBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Finder\Finder;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Event\SuiteEvent,
    Behat\Behat\Event\ScenarioEvent,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Adstacy\AppBundle\Entity\Wall;
use Adstacy\AppBundle\Entity\Ad;
use Adstacy\AppBundle\Entity\User;
use Adstacy\AppBundle\Entity\Image;
use Faker\Factory as FakerFactory;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Feature context.
 */
class BaseContext extends MinkContext
                  implements KernelAwareInterface
{
    protected $kernel;
    protected $parameters;
    protected $faker;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->faker = FakerFactory::create();
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^I have (\d+) ads/
     */
    public function iHaveAds($num)
    {
        $em = $this->get('doctrine')->getManager();
        $walls = $this->getWalls();
        $images = $this->getImages();

        for ($i = 0; $i < $num; $i++) {
            $tags = $this->faker->words($this->faker->randomNumber(0, 10));
            $definedTags = array('promo', 'jual');
            $tags = array_merge($tags, $definedTags);
            $description = $this->faker->sentence($this->faker->randomNumber(1, 10)).implode('#', $tags);
            $wall = $walls[$this->faker->randomNumber(0, count($walls) - 1)];
            $ad = new Ad();
            $ad->setImage($images[$i]);
            $ad->setDescription($description);
            $ad->setWall($wall);
            $em->persist($ad);
        }
        $em->flush();
    }

    /**
     * @Given /^I have users/
     */
    public function iHaveUsers()
    {
        $this->getUsers();
    }

    public function getImages()
    {
        $em = $this->get('doctrine')->getManager();
        $finder = new Finder();
        $images = array();
        $path = __DIR__.'/../../DataFixtures/img';

        foreach ($finder->files()->in($path) as $img) {
            $image = new Image();
            $image->setFile(new UploadedFile($img->getRealPath(), $img->getFilename()));

            $em->persist($image);
            $images[] = $image;
        }

        $em->flush();

        return $images;
    }

    public function getUsers()
    {
        $em = $this->get('doctrine')->getManager();
        $users = array();

        $suwandi = new User();
        $suwandi->setUsername('suwandi');
        $suwandi->setEmail('wandi.lin13@gmail.com');
        $encoder = $this->get('security.encoder_factory')->getEncoder($suwandi);
        $suwandi->setRealName('Suwandi');
        $password = $encoder->encodePassword('suwandi', $suwandi->getSalt());
        $suwandi->setPassword($password);
        $users[] = $suwandi;

        $welly = new User();
        $welly->setUsername('welly');
        $welly->setEmail('wilzichi92@gmail.com');
        $welly->setRealName('Welly');
        $encoder = $this->get('security.encoder_factory')->getEncoder($welly);
        $password = $encoder->encodePassword('welly', $welly->getSalt());
        $welly->setPassword($password);
        $users[] = $welly;

        $erwin = new User();
        $erwin->setUsername('rwinz');
        $erwin->setEmail('rwinz.cyruz@gmail.com');
        $erwin->setRealName('Erwin');
        $encoder = $this->get('security.encoder_factory')->getEncoder($erwin);
        $password = $encoder->encodePassword('erwin', $erwin->getSalt());
        $erwin->setPassword($password);
        $users[] = $welly;

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@termedan.com');
        $admin->addRole('ROLE_SUPER_ADMIN');
        $admin->setRealName('Admin');
        $encoder = $this->get('security.encoder_factory')->getEncoder($admin);
        $password = $encoder->encodePassword('admin', $admin->getSalt());
        $admin->setPassword($password);
        $users[] = $admin;

        $em->persist($suwandi);
        $em->persist($welly);
        $em->persist($erwin);
        $em->persist($admin);
        $em->flush();

        return $users;
    }

    public function getWalls()
    {
        $em = $this->get('doctrine')->getManager();
        $users = $this->getUsers();
        $walls = array();
        $num = $this->faker->randomNumber(2, 10);

        for ($i = 0; $i < $num; $i++) {
            $name = $this->faker->sentence($this->faker->randomNumber(1, 3));
            $user = $users[$this->faker->randomNumber(0, count($users) - 1)];

            $wall = new Wall();
            $wall->setName($name);
            $wall->setUser($user);

            $em->persist($wall);
            $walls[] = $wall;
        }
        $em->flush();

        return $walls;
    }


    /**
     * @param string $entity
     */
    protected function truncate($entity)
    {
        $em = $this->get('doctrine')->getManager();
        $em->createQuery("DELETE FROM AdstacyAppBundle:$entity")->execute();
    }

    /**
     * @BeforeScenario
     */
    public function cleanDB(ScenarioEvent $event)
    {
        $this->truncate('Ad');
        $this->truncate('Wall');
        $this->truncate('User');
        $this->truncate('Image');
    }

    private function get($key)
    {
        return $this->kernel->getContainer()->get($key);
    }
}
