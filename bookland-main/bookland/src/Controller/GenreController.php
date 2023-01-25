<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;

/**
 * @Route("/genre")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("/", name="genre_index", methods={"GET"})
     */
    public function index(GenreRepository $genreRepository): Response
    {
        return $this->render('genre/index.html.twig', [
            'genres' => $genreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="genre_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);
        $errors = array();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('genre_index', [], Response::HTTP_SEE_OTHER);
        } else if($form->isSubmitted() && !$form->isValid()) {
            array_push($errors, $form['nom']->getErrors());
        }

        return $this->render('genre/new.html.twig', [
            'genre' => $genre,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/{id}/edit", name="genre_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Genre $genre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);
        $errors = array();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('genre_index', [], Response::HTTP_SEE_OTHER);
        } else if($form->isSubmitted() && !$form->isValid()) {
            array_push($errors, $form['nom']->getErrors());
        }

        return $this->render('genre/edit.html.twig', [
            'genre' => $genre,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/{id}/delete", name="genre_delete", methods={"POST"})
     */
    public function delete(Request $request, Genre $genre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$genre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($genre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('genre_index', [], Response::HTTP_SEE_OTHER);
    }
    
     // FAIT PAR YANIS

    /**
     * @Route("/action19", name="genre_actions19", methods={"GET", "POST"})
     */
    public function action19(Request $request)
    {
        $form = $this->createFormBuilder()
                        ->add('genre',EntityType::class,  [
                            'class' => Genre::class,
                            'choice_label' => 'nom'
                        ])
                        ->add('envoyer',SubmitType::class)
                        ->getForm();
        $form->handleRequest($request);
        $genre_form=$form->get("genre")->getData();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $nbpagesGeneral = [];
            $livres = $genre_form->getLivres();
            foreach($livres as $livre){
                $nbpages = $livre->getNbpages();
                $nbpagesGeneral[] = $nbpages;
            }
            
            return $this->render('genre/action19_resultat.html.twig', [
                'nbpages' => array_sum($nbpagesGeneral),
                'genres' => $genre_form,
            ]);
        }
        return $this->render('genre/action19.html.twig', [
            'form19' => $form->createView()
        ]);
    }

    /**
     * @Route("/action22", name="genre_actions22", methods={"GET", "POST"})
     */
    public function action22(Request $request)
    {
        $form = $this->createFormBuilder()
                        ->add('genre',EntityType::class,[
                            'class' => Genre::class,
                            'choice_label' => 'nom'
                        ])
                        ->add('envoyer',SubmitType::class)
                        ->getForm();
        $form->handleRequest($request);
        $genre_form=$form->get("genre")->getData();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $nbpagesGeneral = [];
            $livres = $genre_form->getLivres();
            $divnbpages = count($genre_form->getLivres());
            foreach($livres as $livre){  
                $nbpages = $livre->getNbpages();
                $nbpagesGeneral[] = $nbpages;
            }

            
            return $this->render('genre/action22_resultat.html.twig', [
                'nbpages_moyenne' => array_sum($nbpagesGeneral)/$divnbpages,
                'genres' => $genre_form,
            ]);
        }
        return $this->render('genre/action22.html.twig', [
            'form22' => $form->createView()
        ]);
    }


    // FAIT PAR AXEL

    /**
     * @Route("/actionsG", name="genre_action", methods={"GET","POST"})
     */
    public function actionsG(Request $request, GenreRepository $genreRepository, LivreRepository $livreRepository, AuteurRepository $auteurRepository){ 
        $genres = $genreRepository->findAll();
        $auteurs = $auteurRepository->findAll();
        $livres = $livreRepository->findAll();
        $form = $this->action24($request,$genreRepository);
        return $this->render('genre/actionsG.html.twig', [
                      'genres' => $genres,
                      'livres' => $livres,
                      'auteurs' => $auteurs,
                        'form24' => $form,
                       ]);
    }
    
    private function action24(Request $request,GenreRepository $genreRepository )
    {
        $form = $this->createFormBuilder()
                        ->add('envoyer',SubmitType::class)
                        ->getForm();
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted()) {
            $genres = $genreRepository->findAll();
            foreach($genres as $genre)
            {
                $cptLivre = count($genre->getLivres());
                if($cptLivre==0)
                {
                    $entityManager->remove($genre);
                    $entityManager->flush();   
                }
            }
            $this->addFlash('notification', 'Les genres sans livre ont bien été supprimés');
        }
        return $form->createView();
    }
}
