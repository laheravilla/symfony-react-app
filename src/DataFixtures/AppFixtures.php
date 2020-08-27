<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {

        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function getAuthors(ObjectManager $manager)
    {
        $userRepository = $manager->getRepository(User::class);
        return $userRepository->findAll();
    }

    public function loadBlogPosts(ObjectManager $manager)
    {

        $authors = $this->getAuthors($manager);

        for ($i = 0; $i < 10; $i++) {
            $post = (new BlogPost())
                ->setAuthor($authors[array_rand($authors, 1)])
                ->setTitle('My post '.$i)
                ->setSlug('my-post-'.$i)
                ->setCreatedAt(new \DateTime('now'));
            $manager->persist($post);
        }
        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        $authors = $this->getAuthors($manager);
        $isPublishedValues = [true, false];

        for ($i = 0; $i < 20; $i++) {
            $comment = (new Comment())
                ->setAuthor($authors[array_rand($authors, 1)])
                ->setContent('Message content '.$i)
                ->setCreatedAt(new \DateTime('now'))
                ->setIsPublished($isPublishedValues[array_rand($isPublishedValues, 1)]);
            $manager->persist($comment);
        }
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $fullNames = [
            'John Doe',
            'Bob Lastname',
            'Franck Lastname',
            'Robert Lastname',
            'Jo Lastname'
        ];

        for ($i = 0; $i < count($fullNames); $i++) {
            $user = new User();
            if ($i === 0) {
                $user->setUserName('admin');
                $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
                $user->setEmail('admin@mail.com');
                $user->setFullName('Admin');
            } else {
                $user->setUserName($fullNames[$i]);
                $user->setPassword($this->passwordEncoder->encodePassword($user, 'pass'));
                $user->setEmail(strtolower(str_replace(' ', '.', $fullNames[$i])).'@mail.com');
                $user->setFullName($fullNames[$i]);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }
}
