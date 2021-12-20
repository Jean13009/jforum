<?php
namespace App\Service;

use App\Entity\Posts;
use Twig\Environment;
use App\Entity\Topics;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService {
    private $entityClass;
    private $limit = 40;
    private $currentPage = 1;
    private $pages;
    private $manager;
    private $twig;
    private $route;
    private $topic;
    private $category;
    private $data;

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath) {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->topic = $request->getCurrentRequest()->attributes->get('topic');
        $this->category = $request->getCurrentRequest()->attributes->get('category');
        $this->request = $request;
        $this->manager       = $manager;
        $this->twig              = $twig;
        $this->templatePath = $templatePath;
    }

    public function setRoute($route) {
        $this->route = $route;
        return $this;
    }
    public function getRoute() {
        return $this->route;
    }

    public function display() {
        $this->twig->display($this->templatePath, [
            'page'   => $this->currentPage,
            'pages'  => $this->getPages(),
            'route'   => $this->getRoute(),
            'category' => $this->category,
            'topic' => $this->topic
        ]);
    }

    public function getPages() {
        if($this->pages)
        {
            return $this->pages;
        }
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécidifé l'entité sur laquelle paginer");
        }
        $repo = $this->manager->getRepository($this->entityClass);
        
        if($this->entityClass === Posts::class)
        {
        $total = count($repo->findBy(['topic' => $this->topic, 'deleted' => false]));
        }
        else if ($this->entityClass === Topics::class)
        {
        $total = count($repo->findBy(['category' => $this->category, 'deleted' => false]));
        }

        $this->pages = ceil($total / $this->limit);

        return $this->pages;

    }

    public function getData() {

        if($this->data)
        {
            return $this->data;
        }
        
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécidifé l'entité sur laquelle paginer");
        }

        $offset = $this->currentPage * $this->limit - $this->limit;
        $repo = $this->manager->getRepository($this->entityClass);

        if($offset != 0 && $this->entityClass === Posts::class && $this->currentPage <= $this->getPages())
        {
            $offset--;
            $decalLimit = $this->limit + 1;
        }
        else
        {
            $decalLimit = $this->limit;
        }

        if($this->entityClass === Posts::class)
        {
        $this->data = $repo->findBy(['topic' => $this->topic, 'deleted' => false], ['id' => 'ASC'], $decalLimit, $offset);
        }
        else if($this->entityClass === Topics::class)
        {
        $this->data = $repo->findTopicsByLastpost($this->category->getId(), $decalLimit, $offset);
        }

        return $this->data;
    }

    public function setEntityClass($entityClass) {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass() {
        return $this->entityClass;
    }

    public function setLimit($limit) {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function setPage($page) {
        $this->currentPage = $page;

        return $this;
    }

    public function getPage() {
        return $this->currentPage;
    }

    public function setTopic($topic) {
        $this->topic = $topic;

        return $this;
    }

    public function getTopic() {
        return $this->topic;
    }

    public function setCategory($category) {
        $this->category = $category;

        return $this;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getPageByPost($post) {
        $repo = $this->manager->getRepository($this->entityClass);
        $total = $repo->countPosts($post->getTopic()->getId(), $post->getId());

        $this->pages = ceil($total / $this->limit);

        if($total%$this->limit == 0)
        {
            $this->pages++;
            return $this->pages;
        }
        else
            return $this->pages;
    }
}