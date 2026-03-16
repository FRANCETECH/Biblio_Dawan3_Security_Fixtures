<?php

namespace App\Controller\Admin;

use App\Entity\Livre;
use App\Form\LivreType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('admin/livre', name: 'admin.livre.')]
#[IsGranted('ROLE_ADMIN')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, LivreRepository $repository): Response
    {
        $page = $request->query->getInt('page', 1);    
        $livres = $repository->paginatelivres($page );

        return $this->render('admin/livre/index.html.twig', [
            'livres' => $livres
        ]);
    }


    #[Route('/{slug}-{id}', name: 'show', requirements: ['id' => '\d+', 'slug' => "[A-Za-z0-9-'éàùç]*"])]
    public function show(Request $request, string $slug, int $id, LivreRepository $repository): Response
    {
        $livre = $repository->find($id);
        if ($livre->getSlug() !== $slug) {
            return $this->redirectToRoute('admin.livre.show', ['slug' => $livre->getSlug(), 'id' => $livre->getId()]);
        }

        return $this->render('admin/livre/show.html.twig', [
            'livre' => $livre
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Livre $livre, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(LivreType::class, $livre);  
        $form->handleRequest($request);  
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();   
           
            $this->addFlash('success', 'Le livre a bien été modifié');
            return $this->redirectToRoute('admin.livre.index');
        }

        return $this->render('admin/livre/edit.html.twig', [
            'form' => $form,
            'livre' => $livre
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $livre = new livre();
        $form = $this->createForm(livreType::class, $livre);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($livre);
            $em->flush();

            $this->addFlash('success', 'La recette a bien été créee');
            return $this->redirectToRoute('admin.livre.index');
        }

        return $this->render('admin/livre/create.html.twig', [
            'form' => $form
        ]);

    }


    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(livre $livre, EntityManagerInterface $em)
    {
        $em->remove($livre);
        $em->flush(); 
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('livre.index');
    }









}
