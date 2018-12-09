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
            'roles' => [User::ROLE_SUPERADMIN],
        ],
        [
            'username' => 'bobby',
            'email' => 'bobby@gmail.com',
            'name' => 'Bob Sinclar',
            'password' => 'test123T',
            'roles' => [User::ROLE_ADMIN],
        ],
        [
            'username' => 'johny',
            'email' => 'johny@gmail.com',
            'name' => 'John Travolta',
            'password' => 'test123T',
            'roles' => [User::ROLE_WRITER],
        ],
        [
            'username' => 'bless',
            'email' => 'bless_palcal@gmail.com',
            'name' => 'Bless Pascal',
            'password' => 'test123T',
            'roles' => [User::ROLE_WRITER],
        ],
        [
            'username' => 'leo',
            'email' => 'leo@gmail.com',
            'name' => 'Leonardo DiCaprio',
            'password' => 'test123T',
            'roles' => [User::ROLE_EDITOR],
        ],
        [
            'username' => 'russel',
            'email' => 'russel@gmail.com',
            'name' => 'Russel Craw',
            'password' => 'test123T',
            'roles' => [User::ROLE_COMMENTATOR],
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
            $user->setRoles($userTemplate['roles']);
            $this->addReference('user_'.$userTemplate['username'], $user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function loadPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $post = new BlogPost();
            $post->setAuthor($this->getRandomAuthor($post));
            $post->setContent($this->faker->realText());
            $post->setPublished($this->faker->dateTime);
            $post->setSlug($this->faker->slug);
            $post->setTitle($this->faker->realText(30));
            $this->addReference("blog_post_{$i}", $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    private function getRandomAuthor($entity): User
    {
        $user = self::USERS[rand(0, 5)];

        if ($entity instanceof BlogPost
            && !count(array_intersect($user['roles'], [User::ROLE_WRITER, User::ROLE_ADMIN, User::ROLE_SUPERADMIN]))) {
            return $this->getRandomAuthor($entity);
        }

        if ($entity instanceof Comment && !count(array_intersect($user['roles'],
                [User::ROLE_WRITER, User::ROLE_ADMIN, User::ROLE_SUPERADMIN, User::ROLE_COMMENTATOR]))) {
            return $this->getRandomAuthor($entity);
        }

        return $this->getReference('user_'.$user['username']);
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
            $comment->setAuthor($this->getRandomAuthor($comment));
            $manager->persist($comment);
        }
        $manager->flush();
    }
}
