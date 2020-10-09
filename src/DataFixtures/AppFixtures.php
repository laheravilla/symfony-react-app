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

    public function getPosts(ObjectManager $manager)
    {
        $postRepository = $manager->getRepository(BlogPost::class);
        return $postRepository->findAll();
    }

    public function loadBlogPosts(ObjectManager $manager)
    {

        $authors = $this->getAuthors($manager);

        for ($i = 0; $i < 20; $i++) {
            $post = (new BlogPost())
                ->setAuthor($authors[array_rand($authors, 1)])
                ->setTitle('My post '.$i)
                ->setSlug('my-post-'.$i)
                ->setContent("This is my dummy content number ".$i)
                ->setCreatedAt(new \DateTime('now'));
            $manager->persist($post);
        }
        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        $authors = $this->getAuthors($manager);
        $posts = $this->getPosts($manager);
        $isPublishedValues = [true, false];

        for ($i = 0; $i < 30; $i++) {
            $comment = (new Comment())
                ->setAuthor($authors[array_rand($authors, 1)])
                ->setContent('Message content '.$i)
                ->setCreatedAt(new \DateTime('now'))
                ->setPost($posts[array_rand($posts, 1)])
                ->setIsPublished($isPublishedValues[array_rand($isPublishedValues, 1)]);

            $manager->persist($comment);
        }
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $fullNames = [
            'Admin',
            'SuperAdmin',
            'Writer',
            'Editor',
            'John Doe',
            'Bob Lastname',
            'Franck Lastname',
            'Robert Lastname',
            'Jo Lastname'
        ];

        for ($i = 0; $i < count($fullNames); $i++) {
            $user = new User();
            if ($fullNames[$i] === "Admin") {
                $user->setPassword($this->passwordEncoder->encodePassword($user, strtolower($fullNames[$i])));
                $user->setRepeatPassword($this->passwordEncoder->encodePassword($user, strtolower($fullNames[$i])));
                $user->setRoles([User::ROLE_ADMIN]);
            }  elseif ($fullNames[$i] === "SuperAdmin") {
                $user->setPassword($this->passwordEncoder->encodePassword($user, strtolower($fullNames[$i])));
                $user->setRepeatPassword($this->passwordEncoder->encodePassword($user, strtolower($fullNames[$i])));
                $user->setRoles([User::ROLE_SUPER_ADMIN]);
            } elseif ($fullNames[$i] === "Writer") {
                $user->setPassword($this->passwordEncoder->encodePassword($user, strtolower($fullNames[$i])));
                $user->setRepeatPassword($this->passwordEncoder->encodePassword($user, strtolower($fullNames[$i])));
                $user->setRoles([User::ROLE_WRITER]);
            } elseif ($fullNames[$i] === "Editor") {
                $user->setPassword($this->passwordEncoder->encodePassword($user, strtolower($fullNames[$i])));
                $user->setRepeatPassword($this->passwordEncoder->encodePassword($user, strtolower($fullNames[$i])));
                $user->setRoles([User::ROLE_EDITOR]);
            } else {
                $user->setPassword($this->passwordEncoder->encodePassword($user, 'pass'));
                $user->setRepeatPassword($this->passwordEncoder->encodePassword($user, 'pass'));
                $user->setRoles([User::DEFAULT_ROLES]);
            }

            $user->setUsername(strtolower(str_replace(' ', '.', $fullNames[$i])));
            $user->setEmail(strtolower(str_replace(' ', '.', $fullNames[$i])).'@mail.com');
            $user->setFullName($fullNames[$i]);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
