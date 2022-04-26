<?php

namespace App\Entity;

use App\Repository\FlagsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FlagsRepository::class)
 */
class Flags
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Posts::class, inversedBy="flags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Post;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="flags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Category;

    /**
     * @ORM\ManyToOne(targetEntity=Topics::class, inversedBy="flags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Topic;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="flags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $flagColor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Posts
    {
        return $this->Post;
    }

    public function setPost(?Posts $Post): self
    {
        $this->Post = $Post;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->Category;
    }

    public function setCategory(?Categories $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    public function getTopic(): ?Topics
    {
        return $this->Topic;
    }

    public function setTopic(?Topics $Topic): self
    {
        $this->Topic = $Topic;

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

    public function getFlagColor(): ?string
    {
        return $this->flagColor;
    }

    public function setFlagColor(string $flagColor): self
    {
        $this->flagColor = $flagColor;

        return $this;
    }
}
