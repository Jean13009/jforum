<?php

namespace App\Entity;

use Serializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity(repositoryClass=UserRepository::class)
* @ORM\Table(name="`user`")
*/
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
    private $email;
    
    /**
    * @ORM\Column(type="string", length=25)
    */
    private $pseudo;
    
    /**
    * @Gedmo\Timestampable(on="create")
    * @ORM\Column(type="datetime")
    */
    private $createdAt;

    
    /**
    * @ORM\Column(type="json")
    */
    private $roles = [];
    
    /**
    * @var string The hashed password
    * @ORM\Column(type="string")
    */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas confirmé le mot de passe")
     */
    public $passwordConfirm;
    
    /**
    * @ORM\OneToMany(targetEntity=Posts::class, mappedBy="User", orphanRemoval=true)
    */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Flags::class, mappedBy="User", orphanRemoval=true)
     */
    private $flags;

    /**
     * @ORM\OneToMany(targetEntity=Topics::class, mappedBy="User", orphanRemoval=true)
     */
    private $topics;
    
    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->flags = new ArrayCollection();
        $this->topics = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): self
    {
        $this->email = $email;
        
        return $this;
    }
    
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }
    
    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;
        
        return $this;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $post->setUser($this);
        }
        
        return $this;
    }
    
    public function removePost(Posts $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
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
            $flag->setUser($this);
        }

        return $this;
    }

    public function removeFlag(Flags $flag): self
    {
        if ($this->flags->removeElement($flag)) {
            // set the owning side to null (unless already changed)
            if ($flag->getUser() === $this) {
                $flag->setUser(null);
            }
        }

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
            $topic->setUser($this);
        }

        return $this;
    }

    public function removeTopic(Topics $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getUser() === $this) {
                $topic->setUser(null);
            }
        }

        return $this;
    }
}
