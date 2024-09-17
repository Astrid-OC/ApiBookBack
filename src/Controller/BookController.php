<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    #[Route('/api/book', name: 'book', methods: ['GET'])]
    public function getBookList(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList= $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);

        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'detailBook', methods:['GET'])]
    public function getDetailBook(Book $book, SerializerInterface $serializer): JsonResponse
    {
            $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
            return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'deleteBook', methods:['DELETE'])]
    public function deleteBook(Book $book, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($book);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/book', name:"createBook", methods: ['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, AuthorRepository $authorRepository, ValidatorInterface $validator): JsonResponse 
    {
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');
        //on vérif les erreurs
        $errors = $validator->validate($book);

        if ($errors->count() > 0) 
        {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        
        $em->persist($book);
        $em->flush();
        //Récup de l'ensemble des données envoyées sous forme de tableau
        $content = $request->toArray();
        //Récup de l'idAuthor, s'il n'est pas défini, alors on met -1 par défaut.
        $idAuthor = $content['idAuthor'] ?? -1;
        //On cherche l'auteur qui correspond et on l'assigne au livre, si find ne trouve pas, alors null sera retourné.
        $book->setAuthor($authorRepository->find($idAuthor));

        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        $location = $urlGenerator->generate('detailBook', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/book/{id}', name:"updateBook", methods: ['PUT'])]
    public function updateBook(Request $request, SerializerInterface $serializer, Book $currentBook, EntityManagerInterface $em, AuthorRepository $authorRepository): JsonResponse 
    {
        $updateBook = $serializer->deserialize($request->getContent(), Book::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]);
        $content = $request->toArray();
        $idAuthor = $content['idAuthor'] ?? -1;
        $updateBook->setAuthor($authorRepository->find($idAuthor));

        $em->persist($updateBook);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
