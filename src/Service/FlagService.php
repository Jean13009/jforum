<?php
namespace App\Service;

use App\Entity\Flags;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;


class FlagService {
    private $user;
    private $topic;
    private $category;
    private $manager;
    private $paginationservice;

    public function __construct(RequestStack $request, EntityManagerInterface $manager, Security $security)
    {
        $this->topic = $request->getCurrentRequest()->attributes->get('topic');
        $this->category = $request->getCurrentRequest()->attributes->get('category');
        $this->manager = $manager;
        $this->user = $security->getUser();
    }
    public function setPaginationService($paginationservice)
    {
        $this->paginationservice = $paginationservice;
        return $this;
    }

    public function readFlag()
    {
        if(!$this->user)
        {
            return;
        }
        $Posts = $this->paginationservice->getData();
        $lastPosts = end($Posts);

        $repo = $this->manager->getRepository(Flags::class);
        $resultat = $repo->findBy(['Category' => $this->category, 'Topic' => $this->topic, 'User' => $this->user]);
        if (!$resultat)
        {
            $this->writeFlag($lastPosts, "green");
        }
        else if($resultat[0]->getPost()->getId() < $lastPosts->getId())
        {
            $this->updateFlag($resultat[0], $lastPosts);
        }
    }

    public function writeFlag($lastPosts, $flagcolor, $topic = null)
    {
        $flag = new Flags();
        $flag->setCategory($this->category);
        if ($topic)
        {
            $flag->setTopic($topic);
        }
        else
        {
        $flag->setTopic($this->topic);
        }
        $flag->setUser($this->user);
        $flag->setPost($lastPosts);
        $flag->setFlagColor($flagcolor);
        $this->manager->persist($flag);
        $this->manager->flush();
    }

    public function updateFlag($flag, $postReadId, $flagcolor = null)
    {
        $flag->setPost($postReadId);

        if($flagcolor)
        {
            $flag->setFlagColor($flagcolor);
        }
        $this->manager->flush();
    }

    public function newPostFlag($topic, $post)
    {
        $repo = $this->manager->getRepository(Flags::class);
        $resultat = $repo->findBy(['Topic' => $topic, 'User' => $this->user]);
        if (!$resultat)
        {
            $this->writeFlag($post, 'blue', $topic);
        }
        else
        {
            $this->updateFlag($resultat[0], $post, "blue");
        }

    }

    public function getFlagsHome()
    {
        $repo = $this->manager->getRepository(Flags::class);
        $resultat = $repo->findFlagsHomePage($this->user);
        return $resultat;
        //SELECT category_id, MAX(post_id) FROM flags WHERE user_id = 103 GROUP BY category_id ORDER BY MAX(post_id)
    }
}