<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
* @ORM\Entity(repositoryClass=CategoriesRepository::class)
*/
class Categories
{
    /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    private $id;
    
    /**
    * @ORM\Column(type="string", length=255)
    */
    private $titre;
    
    /**
    * @ORM\Column(type="string", length=255)
    */
    private $description;
    
    /**
    * @ORM\OneToMany(targetEntity=Topics::class, mappedBy="category", orphanRemoval=true)
    */
    private $topics;
    
    /**
    * @ORM\Column(type="string", length=255)
    * @Gedmo\Slug(fields={"titre"})
    */
    private $slug;
    
    /**
    * @ORM\OneToMany(targetEntity=Posts::class, mappedBy="category", orphanRemoval=true)
    */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Flags::class, mappedBy="Category", orphanRemoval=true)
     */
    private $flags;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numorder;
    
    
    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->flags = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getTitre(): ?string
    {
        return $this->titre;
    }
    
    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): self
    {
        $this->description = $description;
        
        return $this;
    }
    
    /**
    * @return Collection|Topics[]
    */
    public function getTopics(): Collection
    {
        return $this->topics;
    }
    
    public function addTopic(Topics $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setCategory($this);
        }
        
        return $this;
    }
    
    public function removeTopic(Topics $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getCategory() === $this) {
                $topic->setCategory(null);
            }
        }
        
        return $this;
    }
    
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    
    /**
    * @return Collection|Posts[]
    */
    public function getPosts(): Collection
    {
        return $this->posts;
    }
    
    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setCategory($this);
        }
        
        return $this;
    }
    
    public function removePost(Posts $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }
        
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
            $flag->setCategory($this);
        }

        return $this;
    }

    public function removeFlag(Flags $flag): self
    {
        if ($this->flags->removeElement($flag)) {
            // set the owning side to null (unless already changed)
            if ($flag->getCategory() === $this) {
                $flag->setCategory(null);
            }
        }

        return $this;
    }

    public function getNumorder(): ?int
    {
        return $this->numorder;
    }

    public function setNumorder(?int $numorder): self
    {
        $this->numorder = $numorder;

        return $this;
    }
    
}
