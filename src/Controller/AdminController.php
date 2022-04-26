<?php

namespace App\Controller;

use App\Repository\TrafficRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminController extends AbstractController
{
    /**
    * @Route("/forum/admj", name="admj")
    * @Security("is_granted('ROLE_SUPADMIN')")
    */
    public function track(TrafficRepository $trafficrepository): Response
    {

        return $this->render('home/admin/track.html.twig', [
            'traffics' => $trafficrepository->findBy([],['date' => 'DESC'])
        ]);
    }

    /**
    * @Route("/forum/admj/delete", name="delete")
    * @Security("is_granted('ROLE_SUPADMIN')")
    */
    public function delete(TrafficRepository $trafficrepository, EntityManagerInterface $manager): Response
    {
        $traffics = $trafficrepository->findAll();

        foreach ($traffics as $traffic)
        {
            $manager->remove($traffic);
            $manager->flush();
        }

        return $this->render('home/admin/track.html.twig', [
            'traffics' => $trafficrepository->findBy([],['date' => 'DESC'])
        ]);
    }
}