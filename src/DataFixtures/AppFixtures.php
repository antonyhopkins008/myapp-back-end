<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture {

    const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'name' => 'Anton Pokhodun',
            'password' => 'test123T',
        ],
        [
            'username' => 'bobby',
            'email' => 'bobby@gmail.com',
            'name' => 'Bob Sinclar',
            'password' => 'test123T',
        ],
        [
            'username' => 'johny',
            'email' => 'johny@gmail.com',
            'name' => 'John Travolta',
            'password' => 'test123T',
        ],
        [
            'username' => 'bless',
            'email' => 'bless_palcal@gmail.com',
            'name' => 'Bless Pascal',
            'password' => 'test123T',
        ],
    ];
    private $encoder;
    /**
     * @var \Faker\Factory
     */
    private $faker;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadPosts($manager);
        $this->loadComments($manager);
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userTemplate) {
            $user = new User();
            $user->setEmail($userTemplate['email']);
            $user->setName($userTemplate['name']);
            $user->setPassword($this->encoder->encodePassword($user, $userTemplate['password']));
            $user->setUsername($userTemplate['username']);
            $this->addReference('user_'.$userTemplate['username'], $user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function loadPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $post = new BlogPost();
            $post->setAuthor($this->getRandomAuthor());
            $post->setContent($this->faker->realText());
            $post->setPublished($this->faker->dateTime);
            $post->setSlug($this->faker->slug);
            $post->setTitle($this->faker->realText(30));
            $this->addReference("blog_post_{$i}", $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    private function getRandomAuthor()
    {
        $user = self::USERS[rand(0, 3)]['username'];

        return $this->getReference('user_'.$user);
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $rand = mt_rand(1, 99);
            $post = $this->getReference("blog_post_{$rand}");
            $comment = new Comment();
            $comment->setPublished($this->faker->dateTime);
            $comment->setContent($this->faker->realText());
            $comment->setBlogPost($post);
            $comment->setAuthor($this->getRandomAuthor());
            $manager->persist($comment);
        }
        $manager->flush();
    }
}
