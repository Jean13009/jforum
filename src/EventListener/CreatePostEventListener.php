<?php
// src/EventListener/CreatePostEventListener.php
namespace App\EventListener;

use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

class CreatePostEventListener
{
    private $security;
    private $manager;
    private $requeststack;
    
    public function __construct(Security $security, EntityManagerInterface $manager, RequestStack $requeststack)
    {
        $this->security = $security;
        $this->manager = $manager;
        $this->requeststack = $requeststack;
    }
    
    public function prePersist(Posts $post, LifecycleEventArgs $event): void
    {
        /*$post->setUser($this->security->getUser());
        $post->setDeleted(false);
        foreach ($_COOKIE as $name => $content)
        {
            if (strpos($name, 'quotes') === 0)
            {
                unset($_COOKIE[$name]);
                setcookie($name, '', time() - 3600, '/');
            }
        }
        */
    }
}