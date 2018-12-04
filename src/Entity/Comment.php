<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $blogPost;

    public function getBlogPost(): BlogPost {
        return $this->blogPost;
    }

    public function setBlogPost($blogPost): void {
        $this->blogPost = $blogPost;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(string $content): self {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): self {
        $this->published = $published;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Comment
     */
    public function setAuthor(User $author): self {
        $this->author = $author;

        return $this;
    }
}
