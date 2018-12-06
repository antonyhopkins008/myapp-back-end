<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     itemOperations={"get"},
 *     collectionOperations={
 *          "get",
 *          "post"={"access_control"="is_granted('IS_AUTHENTICATED_FULLY')"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 */
class BlogPost
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $published;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min="20")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="post")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="blogPost")
     */
    private $comment;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $slug;

    /**
     * BlogPost constructor.
     */
    public function __construct() {
        $this->comment = new ArrayCollection();
    }

    public function getComment(): Collection {
        return $this->comment;
    }

    public function setComment($comment): void {
        $this->comment = $comment;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): self {
        $this->published = $published;

        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(string $content): self {
        $this->content = $content;

        return $this;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug): void {
        $this->slug = $slug;
    }

    public function getAuthor() {
        return $this->author;
    }

    /**
     * @param User $author
     * @return BlogPost
     */
    public function setAuthor(User $author): self {
        $this->author = $author;

        return $this;
    }
}
