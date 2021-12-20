<?php
// src/EventListener/TopicEventListener.php
namespace App\EventListener;

use App\Entity\Topics;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

class TopicEventListener
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
    
    public function prePersist(Topics $topic, LifecycleEventArgs $event): void
    {
        $topic->setUser($this->security->getUser());
    }

    public function preFlush(Topics $topic, PreFlushEventArgs $event): void
    {
        if($topic->getDeleted() == true)
        {
            $postsInTopic = $topic->getPosts();
            foreach ($postsInTopic as $postInTopic)
            {
                $postInTopic->setDeleted(true);
                $this->manager->flush($postInTopic);
            }
        }
    }
}