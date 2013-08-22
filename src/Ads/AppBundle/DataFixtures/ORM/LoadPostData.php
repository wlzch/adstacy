<?php

namespace Ads\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Ads\AppBundle\Entity\Post;

class LoadPostData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $posts = array();
        for ($i = 1; $i <= 32; $i++) {
            $tags = $this->faker->words($this->faker->randomNumber(0, 10));
            $definedTags = array('promo', 'jual');
            $tags = array_merge($tags, $definedTags);
            $description = $this->faker->sentence($this->faker->randomNumber(1, 10)).implode('#', $tags);
            $image = $this->getReference('image-'.$i);
            $post = new Post();
            $post->setImage($image);
            $post->setDescription($description);
            $post->setWall($this->getReference('wall-'.$this->faker->randomNumber(1, 15)));
            $posts[] = $post;
            $manager->persist($post);
            $this->addReference('post-'.$i, $post);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
