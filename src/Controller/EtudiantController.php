<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EtudiantRepository;

class EtudiantController extends AbstractController
{

    private $em;
    private $etudiantrep;
    public function __construct(EntityManagerInterface $e, EtudiantRepository $etudiantrep)
    {
        $this->em = $e;
        $this->etudiantrep = $etudiantrep;
    }

    #[Route('/etudiant', name: 'app_etudiant')]
    public function index(): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'controller_name' => 'EtudiantController',
        ]);
    }

    #[Route('/display_etudiant', name: 'display_etudiant')]
    public function display_etudiant(Request $request): Response
    {
        $email = $request->get('search');
        if ($email) {
            $etudiant = $this->em->getRepository(Etudiant::class)->findBy(['email' => $email]);
        } else {
            $etudiant = $this->em->getRepository(Etudiant::class)->findAll();
        }
        return $this->render('etudiant/list.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }


    #[Route('/add_etudiant', name: 'add_etudinat', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($etudiant);
            $this->em->flush();
            $this->addFlash("message", "Etudinat ajouté avec succès.");
            return $this->redirectToRoute('display_etudiant');
        }
        return $this->render('etudiant/form.html.twig', [
            "form" => $form->createView(),
            'is_edit' => false
        ]);
    }

    #[Route('/edit_etudiant/{id}', name: 'edit_etudiant', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id)
    {
        $etudiant = $this->em->getRepository(Etudiant::class)->find($id);
        if (!$etudiant) {
            throw $this->createNotFoundException('L etudiant demandé n\'existe pas.');
        }
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($etudiant);
            $this->em->flush();
            $this->addFlash("message", "etudiant mis à jour avec succès.");
            return $this->redirectToRoute('display_etudiant');
        }
        return $this->render('etudiant/form.html.twig', [
            "form" => $form->createView(),
            'is_edit' => true
        ]);
    }

    #[Route('/delete_etudiant/{id}', name: 'delete_etudiant')]
    public function delete($id)
    {
        $classe = $this->em->getRepository(Etudiant::class)->find($id);
        if (!$classe) {
            throw $this->createNotFoundException('No etudiant found for id ' . $id);
        }
        $this->em->remove($classe);
        $this->em->flush();
        $this->addFlash("message", "deleted");
        return $this->redirectToRoute('display_etudiant');
    }

    #[Route('/tri_etudiant', name: 'tri_etudiant')]
    public function tri()
    {
        return $this->render('etudiant/list.html.twig', [
            'etudiant' => $this->etudiantrep->tri()
        ]);
    }
}
