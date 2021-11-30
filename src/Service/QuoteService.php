<?php

namespace App\Service;

use App\Entity\Posts;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class QuoteService
{
    private $manager;
    private $post;
    
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    function manageQuotes(Posts $post)
    {
        $PostsId = array ();
        foreach ($_COOKIE as $name => $content)
        {
            if (strpos($name, 'quotes') === 0)
            {
                $PostsId[] = $content;
            }
        }
        foreach ($PostsId as $PostId)
        {
            $repo = $this->manager->getRepository(Posts::class);
            $quote = $repo->findOneBy(['id' => $PostId]);
            $quote = $this->deletePreviousQuotes($quote->getContent());
            $quote = "[quotemsg=".$PostId."]".$quote."[/quotemsg]
";
            $post->setContent($post->getContent().$quote);
        }
    }

    public function registerQuotesSql(Posts $post)
    {
        $this->post = $post;
        preg_replace_callback('/(\[quotemsg=)([^\]]+)(\])(.*?[\S+\n\r\s]+?)\[\/quotemsg]/',
        function($matches){ return $this->postsQuotedId($matches); },
        $this->post->getContent());
    }

    private function postsQuotedId($matches)
    {
        $quote = $this->manager->getRepository(Posts::class);
        $quote = $quote->findOneBy(['id' => $matches[2]]);
        $this->post->addQuote($quote);
    }

    private function deletePreviousQuotes($quote)
    {
        return preg_replace('/(\[quotemsg=[\S+\n\r\s]*?.*\[\/quotemsg\])/', '', $quote);
    }
}