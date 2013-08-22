<?php

namespace Ads\AppBundle\Features\Context;

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

use Ads\AppBundle\Entity\Wall;
use Ads\AppBundle\Entity\Post;
use Ads\AppBundle\Entity\User;
use Ads\AppBundle\Entity\Image;
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
     * @Given /^I have (\d+) posts/
     */
    public function iHavePosts($num)
    {
        $this->truncate('Post');
        $em = $this->get('doctrine')->getManager();
        $walls = $this->getWalls();
        $images = $this->getImages();

        for ($i = 0; $i < $num; $i++) {
            $tags = $this->faker->words($this->faker->randomNumber(0, 10));
            $definedTags = array('promo', 'jual');
            $tags = array_merge($tags, $definedTags);
            $description = $this->faker->sentence($this->faker->randomNumber(1, 10)).implode('#', $tags);
            $wall = $walls[$this->faker->randomNumber(0, count($walls) - 1)];
            $post = new Post();
            $post->setImage($images[$i]);
            $post->setDescription($description);
            $post->setWall($wall);
            $em->persist($post);
        }
        $em->flush();
    }

    public function getImages()
    {
        $this->truncate('Image');
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
        $this->truncate('User');
        $em = $this->get('doctrine')->getManager();
        $users = array();

        $suwandi = new User();
        $suwandi->setUsername('suwandi');
        $suwandi->setEmail('wandi.lin13@gmail.com');
        $encoder = $this->get('security.encoder_factory')->getEncoder($suwandi);
        $password = $encoder->encodePassword('suwandi', $suwandi->getSalt());
        $suwandi->setPassword($password);
        $users[] = $suwandi;

        $welly = new User();
        $welly->setUsername('welly');
        $welly->setEmail('wilzichi92@gmail.com');
        $encoder = $this->get('security.encoder_factory')->getEncoder($welly);
        $password = $encoder->encodePassword('welly', $welly->getSalt());
        $welly->setPassword($password);
        $users[] = $welly;

        $erwin = new User();
        $erwin->setUsername('rwinz');
        $erwin->setEmail('rwinz.cyruz@gmail.com');
        $encoder = $this->get('security.encoder_factory')->getEncoder($erwin);
        $password = $encoder->encodePassword('erwin', $erwin->getSalt());
        $erwin->setPassword($password);
        $users[] = $welly;

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@termedan.com');
        $admin->addRole('ROLE_SUPER_ADMIN');
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
        $this->truncate('Wall');
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
        $em->createQuery("DELETE FROM AdsAppBundle:$entity")->execute();
    }

    private function get($key)
    {
        return $this->kernel->getContainer()->get($key);
    }
}
