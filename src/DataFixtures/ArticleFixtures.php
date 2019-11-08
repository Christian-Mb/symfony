<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
       $faker = \Faker\Factory::create('fr_FR');
       for ($i=0; $i<3;$i++){
           $category = new Category();
           $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph())

           ;
           $manager->persist($category);

           for ($j =1, $jMax = mt_rand( 4, 10 ); $j <= $jMax; $j++){
               $article = new Article();
               $content = '<p>' .join($faker->paragraphs(6),'<p></p>').'</p>';

               $article->setTitle($faker->sentence())
                   ->setContent($content)
                   ->setImage($faker->imageUrl())
                   ->setCreatedAt($faker->dateTimeBetween('-8 months'))
                   ->setAuthor($faker->name)
                   ->setGenreLitteraire($faker->sentence(1,true) )
                   ->setCategory($category)
               ;
               $manager->persist($article);

               for ($k=1, $kMax = mt_rand( 4, 15 ); $k<= $kMax; $k++){
                   $comment = new Comment();
                   $content = '<p>' .join($faker->paragraphs(6),'<p></p>').'</p>';
                   $now = new \DateTime();
                 $days=  (new \DateTime()) ->diff($article->getCreatedAt())->days;


                   $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-'.$days.' days'))
                            ->setArticle($article);
                   $manager->persist($comment);
               }


           }
       }


        $manager->flush();
    }
}
