<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\User;
use App\Entity\Author;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {  
        //Création d'un user Normal.
        $user = new User();
        $user->setEmail("user@bookapi.com");
        $user->setRoles(["ROLE_USER"]);

        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);

        //création d'un user Admin
        $userAdmin = new User();
        $userAdmin->setEmail("admin@bookapi.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);

        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);
        
        //Création des auteurs.
        $listAuthor = [];
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

        //Création d'une vingtaine de livres ayant pour titre
        for ($i=0; $i < 20; $i++) { 
            $livre = new Book;
            $livre->setTitre('Livre' . $i);
            $livre->setCoverText('Quatrième de couverture numéro : ' . $i); 
            $livre->setComment("Commentaire du bibliothécaire" . $i);
            //On lie le livre à un auteur pris au hasard dans le tableau des auteurs.
            $livre->setAuthor($listAuthor[array_rand($listAuthor)]);
            $manager->persist($livre);
        }
        $manager->flush();
    }
}
