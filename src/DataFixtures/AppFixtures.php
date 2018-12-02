<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture {
    public function load(ObjectManager $manager) {
        $post = new BlogPost();
        $post->setAuthor("Antony Hopkins");
        $post->setContent("This is the first blog post.");
        $post->setPublished((new \DateTime('20-12-2018 23:00:00')));
        $post->setSlug("first-blog");
        $post->setTitle("First post");
        $manager->persist($post);

        $post = new BlogPost();
        $post->setAuthor("Edik Rozenberg");
        $post->setContent("This is the second blog post.");
        $post->setPublished(new \DateTime('20-11-2018 20:21:00'));
        $post->setSlug("second-blog");
        $post->setTitle("Second post");
        $manager->persist($post);

        $manager->flush();
    }
}
