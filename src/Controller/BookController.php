<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    #[Route('/api/book', name: 'book', methods: ['GET'])]
    public function getBookList(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList= $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($bookList, 'json');

        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'detailBook', methods:['GET'])]
    public function getDetailBook(Book $book, SerializerInterface $serializer): JsonResponse
    {
            $jsonBook = $serializer->serialize($book, 'json');
            return new JsonResponse($jsonBook, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
