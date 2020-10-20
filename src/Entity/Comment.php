<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 *
 * @ApiResource(
 *     attributes={"order"={"createdAt":"DESC"}, "pagination_enabled"=true},
 *     itemOperations={
 *          "get",
 *          "put"={
 *              "access_control"="is_granted('ROLE_EDITOR') or (is_granted('ROLE_COMMENTATOR') and object.getAuthor() === user)"
 *          }
 *      },
 *     collectionOperations={
 *          "get",
 *          "post"={
 *              "access_control"="is_granted('ROLE_COMMENTATOR')"
 *          },
 *      },
 *     subresourceOperations={
 *          "api_blog_posts_comments_get_subresource"={
 *               "normalization_context"={
 *                  "groups"={"get-comment-with-author"}
 *              }
 *          }
 *     },
 *     denormalizationContext={
 *          "groups"={"post"}
 *     }
 * )
 */
class Comment implements AuthoredEntityInterface, CreatedAtEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-comment-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=20, max=3000)
     * @Groups({"post", "get-comment-with-author"})
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     * @Groups({"get-comment-with-author", "post"})
     */
    private $isPublished;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-comment-with-author", "post"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-comment-with-author"})
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return CreatedAtEntityInterface
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): CreatedAtEntityInterface
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPost(): BlogPost
    {
        return $this->post;
    }

    public function setPost(BlogPost $post): self
    {
        $this->post = $post;
        return $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function getAuthor(): UserInterface
    {
        return $this->author;
    }

    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;
        return $this;
    }
}
