<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {  //Création des auteurs.
        $listAuthor = [];

        //Création d'une vingtaine de livres ayant pour titre
        for ($i=0; $i < 20; $i++) { 
            $livre = new Book;
            $livre->setTitre('Livre' . $i);
            $livre->setCoverText('Quatrième de couverture numéro : ' . $i); 
            //On lie le livre à un auteur pris au hasard dans le tableau des auteurs.
            $livre->setAuthor($listAuthor[array_rand($listAuthor)]);
            $manager->persist($livre);
        }

        for ($i=0; $i < 10; $i++) 
        { 
            //Création de l'auteur lui-même.
            $author = new Author();
            $author->setPrenom("Prénom" . $i);
            $author->setNom("Nom" . $i);
            $manager->persist($author);
            //On sauvegarde l'auteur créer dans un tableau.
            $listAuthor[] = $author;
        }

        $manager->flush();
    }
}
