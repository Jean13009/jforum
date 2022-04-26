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
        
        if (isset($_SERVER['HTTP_CLIENT_IP']) && array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ips = array_map('trim', $ips);
            $ip = $ips[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        $ip = ($ip === false) ? '0.0.0.0' : $ip;
        $traffic->setIP($ip);
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