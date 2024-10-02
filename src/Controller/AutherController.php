<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AuthorRepository;


class AutherController extends AbstractController
{

    private $Auther;

    public function __construct(AuthorRepository $AuthorRepository)
    {
        $this->Auther = $AuthorRepository;
    }

    #[Route('/auther', name: 'app_auther', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('auther/index.html.twig', [
            'controller_name' => 'AutherController',
        ]);
    }

    #[Route('/showauther/{name}', name: 'showauther', defaults: ['name' => 'monsef'], methods: ['GET'])]
    public function showAuther($name): Response
    {
        return $this->render('auther/show.html.twig', [
            'name' => $name,
        ]);
    }

    #[Route('/listauther', name: 'listAuthers', methods: ['GET'])]
    public function listAuthers(): Response
    {
        return $this->render('auther/list.html.twig', ["authors" => $this->Auther->listAuthors()]);
    }

    #[Route('/autherDetail/{id}', name: 'DetailAuthers', methods: ['GET'])]
    public function authorDetails($id): Response
    {

        return $this->render('auther/showAuthor.html.twig', ["author" => $this->Auther->AuthorById($id)]);
    }
}
