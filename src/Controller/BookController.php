<?php

namespace App\Controller;

use App\Form\BookType;
use App\Entity\Book;
use App\Entity\Author;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;


class BookController extends AbstractController
{
    private $em;
    private $book;
    public function __construct(EntityManagerInterface $e, BookRepository $book)
    {
        $this->em = $e;
        $this->book = $book;
    }

    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }


    #[Route('/addbook', name: 'add_book', methods: ['GET', 'POST'])]
    public function addBook(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $book->setEnabled(true);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($book);
            $this->em->flush();

            $author = $book->getAuthors();

            if ($author) {
                $author->setNbBooks($author->getNbBooks() + 1);
                $this->em->persist($author);
                $this->em->flush();
                $this->addFlash("message", "added");
                return $this->redirectToRoute('display_books');
            }
        }
        return $this->render('book/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false
        ]);
    }

    #[Route('/display_books', name: 'display_books',)]
    public function display_books(): Response
    {
        $books = $this->em->getRepository(Book::class)->findBy(['enabled' => true]);

        $publishedCount = $this->em->getRepository(Book::class)->count(['enabled' => true]);

        $unpublishedCount = $this->em->getRepository(Book::class)->count(['enabled' => false]);

        return $this->render('book/list.html.twig', [
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
            'books' => $books,
        ]);
    }

    #[Route('/editbook/{id}', name: 'edit_book')]
    public function editBook(Request $request, $id)
    {
        $book = $this->em->getRepository(Book::class)->find($id);
        $form = $this->createForm(BookType::class, $book, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($book);
            $this->em->flush();
            $this->addFlash("message", "updated");
            return $this->redirectToRoute('display_books');
        }

        return $this->render('book/form.html.twig', [
            "form" => $form->createView(),
            'is_edit' => true
        ]);
    }

    #[Route('/deleteBook/{id}', name: 'delete_book')]
    public function deleteBook($id)
    {
        $book = $this->em->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('No book found for id ' . $id);
        }

        $author = $book->getAuthors();

        if ($author) {
            $currentNbBooks = $author->getNbBooks();
            if ($currentNbBooks > 0) {
                $author->setNbBooks($currentNbBooks - 1);
                $this->em->persist($author);
            }
        }

        $this->em->remove($book);
        $this->em->flush();

        $this->addFlash("message", "deleted");

        return $this->redirectToRoute('display_books');
    }


    #[Route('/deleteAuthorsWithNoBooks', name: 'delete_authors_with_no_books')]
    public function deleteAuthorsWithNoBooks(): Response
    {
        $authors = $this->em->getRepository(Author::class)->findBy(['nbBooks' => 0]);
        foreach ($authors as $author) {
            $this->em->remove($author);
        }
        $this->em->flush();
        $this->addFlash("message", count($authors) . " auteur(s) supprimé(s) dont le nb_books est égal à zéro.");

        return $this->redirectToRoute('display_authors');
    }

    #[Route('/detailbook/{id}', name: 'detail_book')]
    public function bookDetails($id)
    {
        $book = $this->em->getRepository(Book::class)->find($id);
        return $this->render('book/bookdetail.html.twig', [
            "book" => $book,
        ]);
    }
}
