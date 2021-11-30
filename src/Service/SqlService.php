<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class SqlService
{
    private $db;
    
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    public function allFlagsList($user, $color)
    {
        $sql = "SELECT flags.id AS id, flags.flag_color AS color, flags.post_id AS flagPostId, posts.user_id AS lastPostUserId, posts.id AS lastPostId, posts.created_at AS lastPostCreated, user.pseudo AS lastPostUserPseudo, flags.category_id AS flagCategoryId, categories.slug AS catSlug, COUNT(p2.id) AS postCount, topics.slug AS topicSlug, topics.titre AS topicTitre, u2.pseudo AS topicUser from flags inner join posts on flags.topic_id = posts.topic_id and posts.id=(select max(posts.id) from posts where flags.topic_id = posts.topic_id) JOIN posts p2 ON flags.topic_id = p2.topic_id JOIN topics ON flags.topic_id = topics.id JOIN user ON user.id = posts.user_id JOIN user u2 ON u2.id = topics.user_id JOIN categories ON categories.id = flags.category_id WHERE flags.user_id = ? AND flags.flag_color = ? GROUP BY flags.id, posts.id ORDER BY MAX(p2.id) DESC";
        $prep = $this->db->prepare($sql);
        $prep->bindValue(1, $user);
        $prep->bindValue(2, $color);
        $result = $prep->execute();
        return $result->fetchAllAssociative();
    }

    public function flagsListByCategory($user, $category, $color = null)
    {
        if ($color)
        {
            $sql = "SELECT flags.id AS id, flags.flag_color AS color, flags.post_id AS flagPostId, posts.user_id AS lastPostUserId, posts.id AS lastPostId, posts.created_at AS lastPostCreated, user.pseudo AS lastPostUserPseudo, flags.category_id AS flagCategoryId, categories.slug AS catSlug, COUNT(p2.id) AS postCount, topics.slug AS topicSlug, topics.titre AS topicTitre, u2.pseudo AS topicUser from flags inner join posts on flags.topic_id = posts.topic_id and posts.id=(select max(posts.id) from posts where flags.topic_id = posts.topic_id) JOIN posts p2 ON flags.topic_id = p2.topic_id JOIN topics ON flags.topic_id = topics.id JOIN user ON user.id = posts.user_id JOIN user u2 ON u2.id = topics.user_id JOIN categories ON categories.id = flags.category_id WHERE flags.user_id = ? AND flags.flag_color = ? AND flags.category_id = ? GROUP BY flags.id, posts.id ORDER BY MAX(p2.id) DESC";
            $prep = $this->db->prepare($sql);
            $prep->bindValue(1, $user);
            $prep->bindValue(2, $color);
            $prep->bindValue(3, $category);
            $result = $prep->execute();
            return $result->fetchAllAssociative();
        }
        else
        {
            $sql = "SELECT flags.id AS id, flags.flag_color AS color, flags.post_id AS flagPostId, posts.user_id AS lastPostUserId, posts.id AS lastPostId, posts.created_at AS lastPostCreated, user.pseudo AS lastPostUserPseudo, flags.category_id AS flagCategoryId, categories.slug AS catSlug, COUNT(p2.id) AS postCount, topics.slug AS topicSlug, topics.titre AS topicTitre, u2.pseudo AS topicUser from flags inner join posts on flags.topic_id = posts.topic_id and posts.id=(select max(posts.id) from posts where flags.topic_id = posts.topic_id) JOIN posts p2 ON flags.topic_id = p2.topic_id JOIN topics ON flags.topic_id = topics.id JOIN user ON user.id = posts.user_id JOIN user u2 ON u2.id = topics.user_id JOIN categories ON categories.id = flags.category_id WHERE flags.user_id = ? AND flags.category_id = ? GROUP BY flags.id, posts.id ORDER BY MAX(p2.id) DESC";
            $prep = $this->db->prepare($sql);
            $prep->bindValue(1, $user);
            $prep->bindValue(2, $category);
            $result = $prep->execute();
            return $result->fetchAllAssociative();
        }
    }
}