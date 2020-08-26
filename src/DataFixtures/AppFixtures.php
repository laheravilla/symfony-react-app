<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $post = (new BlogPost())
                ->setAuthor('Author '.$i)
                ->setTitle('My post '.$i)
                ->setSlug('my-post-'.$i)
                ->setCreatedAt(new \DateTime('now'));
            $manager->persist($post);
        }
        $manager->flush();
    }
}
