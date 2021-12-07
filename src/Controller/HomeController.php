<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Topics;
use App\Form\NewPostType;
use App\Entity\Categories;
use App\Form\NewTopicType;
use App\Service\SqlService;
use App\Service\FlagService;
use App\Service\QuoteService;
use App\Service\PaginationService;
use App\Repository\FlagsRepository;
use App\Repository\TopicsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends AbstractController
{
    /**
    * @Route("/", name="home")
    */
    public function categories(CategoriesRepository $categoriesrepository, FlagService $flag): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $categoriesrepository->findAll(),
            'flags' => $flag->getFlagsHome()
        ]);
    }
    
    /**
    * @Route("/flags/{color}", name="allFlagsList")
    */
    public function allFlagsList(CategoriesRepository $categoriesrepository, PaginationService $pagination, SqlService $sql, $color): Response
    {
        $flag = $sql->allFlagsList($this->getUser()->getId(), $color);
        return $this->render('home/flags/allflag_list.html.twig', [
            'categories' => $categoriesrepository->findAll(),
            'pagination' => $pagination,
            'flags' => $flag
        ]);
    }
    
    /**
    * @Route("/flags/{slug}/{color}", name="flag_by_category")
    */
    public function flagsByCategory(Categories $category, PaginationService $pagination, SqlService $sql, $color): Response
    {
        $flags = $sql->flagsListByCategory($this->getUser()->getId(), $category->getId(), $color);
        
        return $this->render('home/topics/topics_lists_flags.html.twig', [
            'category' => $category,
            'pagination' => $pagination,
            'flags' => $flags
        ]);
    }
    
    /**
    * @Route("/topics_list/{slug}/{page}", name="topics_list", requirements={"page": "\d+"})
    */
    public function topics($page = 1, Categories $category, PaginationService $pagination, SqlService $sql): Response
    {
        if($this->getUser())
        $flags = $sql->flagsListByCategory($this->getUser()->getId(), $category->getId());
        else
        $flags = null;
        $pagination->setEntityClass(Topics::class);
        
        if($pagination->getPages() < $page)
        {
            return $this->redirectToRoute('topics_list', [
                'slug' => $category->getSlug(),
                'page' => $pagination->getPages()
            ]);
        }
        else
        {
            $pagination->setPage($page);
        }   
        
        return $this->render('home/topics/topics_lists.html.twig', [
            'pagination' => $pagination,
            'flags' => $flags,
            'category' => $category
        ]);
    }
    
    /**
    * @Route("/new_topic/{slug}", name="new_topic")
    */
    public function newtopic(Categories $category, Request $request, EntityManagerInterface $manager, FlagService $flag, QuoteService $quoteservice): Response
    {
        $post = new Posts();
        $quoteservice->manageQuotes($post);
        
        $form = $this->createForm(NewTopicType::class, $post);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) 
        {
            $quoteservice->registerQuotesSql($post);
            $topic = $post->getTopic();
            $topic->setCategory($category);
            $topic->setUser($this->getUser());
            $post->setCategory($category);
            $manager->persist($post);
            $manager->persist($topic);
            $manager->flush();
            $flag->newPostFlag($topic, $post);
            
            return $this->redirectToRoute('home');
        }
        
        return $this->render('home/posts/new_post.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
    * @Route("/new_post/{category_slug}/{topic_slug}", name="new_post")
    * @ParamConverter("category", options={"mapping": {"category_slug": "slug"}})
    * @ParamConverter("topic", options={"mapping": {"topic_slug": "slug"}})
    */
    public function newpost(Categories $category, Topics $topic, Request $request, EntityManagerInterface $manager, FlagService $flag, QuoteService $quoteservice): Response
    {
        $post = new Posts();
        $post->setCategory($category);
        $post->setTopic($topic);
        $quoteservice->manageQuotes($post);

        $form = $this->createForm(NewTopicType::class, $post);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) 
        {
            $quoteservice->registerQuotesSql($post);
            $manager->persist($post);
            $manager->flush();
            $flag->newPostFlag($topic, $post);
            
            return $this->redirectToRoute('post', [
                'id' => $post->getId()
            ]);
        }
        
        return $this->render('home/posts/new_post.html.twig', [
            'form' => $form->createView(),
            'topic' => $topic
        ]);
    }
    /**
    * @Route("/edit/{topic_slug}/{post_id}", name="post_edit")
    * @Security("is_granted('ROLE_USER') and user === post.getUser()", message="Vous ne pouvez pas modifier cette annonce")
    * @ParamConverter("post", options={"mapping": {"post_id": "id"}})
    * @ParamConverter("topic", options={"mapping": {"topic_slug": "slug"}})
    */
    public function edit(Posts $post, Topics $topic, Request $request, EntityManagerInterface $manager, QuoteService $quoteservice)
    {
        $form = $this->createForm(NewTopicType::class, $post);
                    
        $form->handleRequest($request);
                    
        if( $form->isSubmitted() && $form->isValid() )
        {
            $quoteservice->registerQuotesSql($post);
            $manager->flush($post);
            $manager->flush($topic);
                        
            return $this->redirectToRoute('post', [
            'id' => $post->getId()
            ]);
        }
                        
        return $this->render('home/posts/new_post.html.twig', [
        'form' => $form->createView(),
        'post' => $post,
        'topic' => $topic
        ]);
    }
    
    /**
    * @Route("/posts_list/{slug}/{topic_slug}/{page}", name="posts_list", requirements={"page": "\d+"})
    * @ParamConverter("category", options={"mapping": {"slug": "slug"}})
    * @ParamConverter("topic", options={"mapping": {"topic_slug": "slug"}})
    */
    public function posts($page = 1, Request $request, Categories $category, Topics $topic, PaginationService $pagination, FlagService $flag): Response
    {
        
        
        $pagination->setEntityClass(Posts::class);
        
        if($pagination->getPages() < $page)
        {
            return $this->redirectToRoute('posts_list', [
                'slug' => $category->getSlug(),
                'topic_slug' => $topic->getSlug(),
                'page' => $pagination->getPages()
            ]);
        }
        else
        {
            $pagination->setPage($page);
        }       
        
        $flag->setPaginationService($pagination);
        $flag->readFlag();
        
        return $this->render('home/posts/posts_lists.html.twig', [
            'pagination' => $pagination,
            'topic' => $topic,
            'category' => $category
        ]);
    }
    
    /**
    * @Route("/post/{id}", name="post")
    */
    public function post(Posts $post = null, PaginationService $pagination, Request $request): Response
    {
        if(!$post)
        {
            return $this->redirectToRoute('home');
        }
        
        $pagination->setEntityClass(Posts::class);
        $page = $pagination->getPageByPost($post);
        
        return $this->redirectToRoute('posts_list', [
            'slug' => $post->getCategory()->getSlug(),
            'topic_slug' => $post->getTopic()->getSlug(),
            'page' => $page,
            '_fragment' => $post->getId()
        ]);
    }
    /**
    * @Route("/post_quotes/{id}", name="post_quotes")
    */
    public function postQuotes(Posts $post): Response
    {
        return $this->render('home/posts/post_quotes.html.twig', [
            'quotes' => $post->getQuotes()
        ]);
    }
}
