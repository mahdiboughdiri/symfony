<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class ClasseController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $e)
    {
        $this->em = $e;
    }
    #[Route('/classe', name: 'app_classe')]
    public function index(): Response
    {
        return $this->render('classe/index.html.twig', [
            'controller_name' => 'ClasseController',
        ]);
    }

    #[Route('/', name: 'display_classes')]
    public function display_(): Response
    {
        $classes = $this->em->getRepository(Classe::class)->findAll();
        return $this->render('classe/list.html.twig', [
            'classes' => $classes,
        ]);
    }

    #[Route('/detail_classe/{id}', name: 'detail_classe')]
    public function detail_classe($id): Response
    {
        $classes = $this->em->getRepository(Classe::class)->find($id);
        return $this->render('etudiant/list.html.twig', [
            'etudiant' => $classes->getEtudiants()
        ]);
    }

    #[Route('/add_classe', name: 'add_classe', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $this->em->getRepository(Classe::class)->findOneBy(['nom' => $classe->getNom()]);

            if ($existing) {
                $this->addFlash("message", "Le nom existe déjà, veuillez en choisir un autre.");
                return $this->redirectToRoute('display_classes');
            } else {
                $this->em->persist($classe);
                $this->em->flush();
                $this->addFlash("message", "Classe ajouté avec succès.");
                return $this->redirectToRoute('display_classes');
            }
        }
        return $this->render('classe/form.html.twig', [
            "form" => $form->createView(),
            'is_edit' => false
        ]);
    }

    #[Route('/edit_classe/{id}', name: 'edit_classe', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id)
    {
        $classe = $this->em->getRepository(Classe::class)->find($id);
        if (!$classe) {
            throw $this->createNotFoundException('Le classe demandé n\'existe pas.');
        }
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $this->em->getRepository(Classe::class)->findOneBy(['nom' => $classe->getNom()]);

            if ($existing && $existing->getId() !== $classe->getId()) {
                $this->addFlash("message", "Le nom existe déjà, veuillez en choisir un autre.");
                return $this->redirectToRoute('display_classes');
            } else {
                $this->em->persist($classe);
                $this->em->flush();
                $this->addFlash("message", "classe mis à jour avec succès.");
                return $this->redirectToRoute('display_classes');
            }
        }
        return $this->render('classe/form.html.twig', [
            "form" => $form->createView(),
            'is_edit' => true
        ]);
    }

    #[Route('/delete_classe/{id}', name: 'delete_classe')]
    public function delete($id)
    {
        $classe = $this->em->getRepository(Classe::class)->find($id);
        if (!$classe) {
            throw $this->createNotFoundException('No Classe found for id ' . $id);
        }
        $this->em->remove($classe);
        $this->em->flush();
        $this->addFlash("message", "deleted");
        return $this->redirectToRoute('display_classes');
    }
}
