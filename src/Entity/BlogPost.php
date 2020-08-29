<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Composition\AuthorComposition;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 *
 * * Allows GET method. All non listed methods do not have a route
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "put"={
                "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object.getAuthor() === user"
 *          }
 *      },
 *     collectionOperations={
 *          "get",
 *          "post"={
                "access_control"="is_granted('IS_AUTHENTICATED_FULLY')"
 *          }
 *      }
 * )
 */
class BlogPost
{
    use AuthorComposition;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $slug;

    public function __constructor()
    {
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
