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

    private $encoder;
    /**
     * @var \Faker\Factory
     */
    private $faker;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager) {
        $this->loadUsers($manager);
        $this->loadPosts($manager);
        $this->loadComments($manager);
    }

    public function loadComments(ObjectManager $manager) {
        $user = $this->getReference('admin_user');

        for ($i = 0; $i < 100; $i++) {
            $rand = mt_rand(1, 99);
            $post = $this->getReference("blog_post_{$rand}");
            $comment = new Comment();
            $comment->setPublished($this->faker->dateTime);
            $comment->setContent($this->faker->realText());
            $comment->setBlogPost($post);
            $comment->setAuthor($user);
            $manager->persist($comment);
        }
        $manager->flush();
    }

    public function loadPosts(ObjectManager $manager) {
        $user = $this->getReference('admin_user');

        for ($i = 0; $i < 100; $i++) {
            $post = new BlogPost();
            $post->setAuthor($user);
            $post->setContent($this->faker->realText());
            $post->setPublished($this->faker->dateTime);
            $post->setSlug($this->faker->slug);
            $post->setTitle($this->faker->realText(30));
            $this->addReference("blog_post_{$i}", $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager) {
        $user = new User();
        $user->setEmail('antony.hopkins008@gmail.com');
        $user->setName('Anton Pokhodun');
        $user->setPassword($this->encoder->encodePassword($user, 'test123T'));
        $user->setUsername('admin');
        $this->addReference('admin_user', $user);
        $manager->persist($user);
        $manager->flush();
    }
}
