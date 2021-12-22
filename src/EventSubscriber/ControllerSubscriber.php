<?php
// src/EventSubscriber/ControllerSubscriber.php
namespace App\EventSubscriber;

use DateTime;
use App\Entity\Traffic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ControllerSubscriber implements EventSubscriberInterface
{
    private $request;
    private $manager;

    public function __construct(EntityManagerInterface $manager, RequestStack $request)
    {
        $this->request = $request;
        $this->manager = $manager;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $traffic = new Traffic();
        $traffic->setIP($this->request->getMasterRequest()->getClientIp());
        $page = $event->getController();
        $traffic->setPage($page[1]);

        if($traffic->getPage() != 'toolbarAction')
        {
            $this->manager->persist($traffic);
            $this->manager->flush($traffic);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}