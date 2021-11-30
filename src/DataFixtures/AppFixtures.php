<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Posts;
use App\Entity\Topics;
use App\Entity\Categories;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
    * Encodeur de mots de passe
    *
    * @var UserPasswordEncoderInterface
    */
    private $encoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        $user = new User();
        $hash = $this->encoder->encodePassword($user, "pass");
        
        $user->setPseudo('admin')
        ->setEmail('jo@jo.com')
        ->setPassword($hash)
        ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        
        
        $manager->persist($user);
        
        
        for ($i = 0; $i < 5; $i++) {
            $categories = new Categories();
            
            $categories->setTitre($faker->word())
            ->setDescription($faker->word())
            ->setnumorder($i);
            
            
            $manager->persist($categories);
            
            for ($j = 0; $j < 5; $j++) {
                $user = new User();
                $hash = $this->encoder->encodePassword($user, "pass");
                
                $user->setPseudo($faker->firstName())
                ->setEmail($faker->email())
                ->setPassword($hash);
                
                
                $manager->persist($user);
                
                for ($k = 0; $k < 5; $k++) {
                    $topics = new Topics();
                    
                    $topics->setTitre($faker->word())
                    ->setUser($user)
                    ->setCategory($categories);
                    
                    
                    $manager->persist($topics);
                    for ($l = 0; $l < 60; $l++) {
                        $post = new Posts();
                        
                        $post->setContent($faker->text(200))
                        ->setTopic($topics)
                        ->setUser($user)
                        ->setCategory($categories)
                        ->setDeleted(false);
                        
                        
                        $manager->persist($post);
                    }
                }
            }
            
        }
        
        $manager->flush();
    }
}
