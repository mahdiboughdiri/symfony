<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AuthorRepository;
use App\Form\AuthorType;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthorController extends AbstractController
{
    private $authorRepository;
    private $em;

    public function __construct(AuthorRepository $AuthorRepository, EntityManagerInterface $e)
    {
        $this->authorRepository = $AuthorRepository;
        $this->em = $e;
    }

    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/listAuthor', name: 'list_author', methods: ['GET'])]
    public function listAuthor(): Response
    {
        return $this->render('author/listauthor.html.twig', [
            'authors' => $this->authorRepository->listAuthors()
        ]);
    }

    #[Route('/addStaticAuthor', name: 'add_Static_Author', methods: ['GET'])]
    public function AddAuthor(): Response
    {
        $this->authorRepository->addStaticAuthor($this->em);
        return $this->render('author/add.html.twig', [
            'msg' => "ok"
        ]);
    }

    #[Route('/addAuther', name: 'add_author')]
    public function creatAuther(Request $request)
    {
        $a = new Author();
        $form = $this->createForm(AuthorType::class, $a);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($a);
            $this->em->flush();
            $this->addFlash("message", "added");
            return $this->redirectToRoute('list_author');
        }
        return $this->render('author/form.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/editAuther/{id}', name: 'edit_author')]
    public function editAuther(Request $request, $id)
    {
        $author = $this->em->getRepository(Author::class)->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($author);
            $this->em->flush();
            $this->addFlash("message", "updated");
            return $this->redirectToRoute('list_author');
        }

        return $this->render('author/form.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/deleteAuther/{id}', name: 'delete_author')]
    public function deleteAuther($id)
    {
        $author = $this->em->getRepository(Author::class)->find($id);

        $this->em->remove($author);
        $this->em->flush();

        $this->addFlash("message", "Deleted");

        return $this->redirectToRoute('list_author');
    }
}
