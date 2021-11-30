<?php

namespace App\Form\EventListener;

use App\Entity\Posts;
use App\Form\NewTitleTopicType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddTopicFieldSubscriber implements EventSubscriberInterface
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event): void
    {
        $post = $event->getData();
        $form = $event->getForm();

        $repo = $this->manager->getRepository(Posts::class);

        if($post->getTopic())
        $firstPostId = $repo->findOneBy(['topic' => $post->getTopic()])->getId();

        $post = $event->getData();
        $form = $event->getForm();

        if (!$post->getTopic() || $firstPostId == $post->getId()) {
            $form->add('topic', NewTitleTopicType::class);
        }
    }
}