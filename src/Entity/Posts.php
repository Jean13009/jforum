<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=PostsRepository::class)
 */
class Posts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $Content;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Topics::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Flags::class, mappedBy="Post", orphanRemoval=true)
     */
    private $flags;

    /**
     * @ORM\ManyToMany(targetEntity=Posts::class, inversedBy="quotes")
     */
    private $Quote;

    /**
     * @ORM\ManyToMany(targetEntity=Posts::class, mappedBy="Quote")
     */
    private $quotes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    public function __construct()
    {
        $this->flags = new ArrayCollection();
        $this->Quote = new ArrayCollection();
        $this->quotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): self
    {
        $this->Content = $Content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTopic(): ?Topics
    {
        return $this->topic;
    }

    public function setTopic(?Topics $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Flags[]
     */
    public function getFlags(): Collection
    {
        return $this->flags;
    }

    public function addFlag(Flags $flag): self
    {
        if (!$this->flags->contains($flag)) {
            $this->flags[] = $flag;
            $flag->setPost($this);
        }

        return $this;
    }

    public function removeFlag(Flags $flag): self
    {
        if ($this->flags->removeElement($flag)) {
            // set the owning side to null (unless already changed)
            if ($flag->getPost() === $this) {
                $flag->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getQuote(): Collection
    {
        return $this->Quote;
    }

    public function addQuote(self $quote): self
    {
        if (!$this->Quote->contains($quote)) {
            $this->Quote[] = $quote;
        }

        return $this;
    }

    public function removeQuote(self $quote): self
    {
        $this->Quote->removeElement($quote);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
