<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * @Route("/auteur")
 */
class AuteurController extends AbstractController
{
    /**
     * @Route("/", name="auteur_index", methods={"GET"})
     */
    public function index(AuteurRepository $auteurRepository): Response
    {
        return $this->render('auteur/index.html.twig', [
            'auteurs' => $auteurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="auteur_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $auteur = new Auteur();
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);
        $errors = array();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($auteur);
            $entityManager->flush();

            return $this->redirectToRoute('auteur_index', [], Response::HTTP_SEE_OTHER);
        } else if($form->isSubmitted() && !$form->isValid()) {
            array_push($errors, $form['nom_prenom']->getErrors());
        }

        return $this->render('auteur/new.html.twig', [
            'auteur' => $auteur,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/show/{id}", name="auteur_show", methods={"GET"})
     */
    public function show(Auteur $auteur): Response
    {
        return $this->render('auteur/show.html.twig', [
            'auteur' => $auteur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="auteur_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Auteur $auteur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);
        $errors = array();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('auteur_index', [], Response::HTTP_SEE_OTHER);
        } else if($form->isSubmitted() && !$form->isValid()) {
            array_push($errors, $form['nom_prenom']->getErrors());
        }

        return $this->render('auteur/edit.html.twig', [
            'auteur' => $auteur,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/{id}/delete", name="auteur_delete", methods={"POST"})
     */
    public function delete(Request $request, Auteur $auteur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$auteur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($auteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('auteur_index', [], Response::HTTP_SEE_OTHER);
    }


    // FAIT PAR BAPTISTE/AXEL

    /**
     * @Route("/actionsA", name="actionA", methods={"GET", "POST"})
     */
    public function actionsA(Request $request, AuteurRepository $auteurRepository,GenreRepository $genreRepository,LivreRepository $livreRepository)
    {
        $action16 = $this->action16($auteurRepository);
        $action20 = $this->action20();
        $genres = $genreRepository->findAll();
        $auteurs = $auteurRepository->findAll();
        $livres = $livreRepository->findAll();
        return $this->render('auteur/actionsA.html.twig', [
            'action16' => $action16,
            'genres' => $genres,
            'auteurs' => $auteurs,
            'livres' => $livres,
            'action20' => $action20,
        ]);
    }


    //FAIT PAR BAPTISTE

    // Actions avec l'auteur
    private function action16(AuteurRepository $auteurRepository)
    {
        $auteurs = $auteurRepository->findAll();
        $auteursAvecLivre = [];
        foreach($auteurs as $auteur) {
            $nblivres = count($auteur->getLivres());
            if($nblivres >= 3) {
                $auteursAvecLivre[] = $auteur;
            }
        }

        return $auteursAvecLivre;
    }

    
    // FAIT PAR AXEL

    private function action20()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Livre::class);
        $livrechrono = $repo->Reqaction20();
        $action20 = [];
        
        foreach($livrechrono as $livre)
           {
            $action20= $livre->getGenres();
           }
        return $action20;
    }

   


    // FAIT PAR YANIS

    /**
     * @Route("/action26", name="livre_action26", methods={"GET", "POST"})
     */
    public function action26(Request $request)
    {
        $form = $this->createFormBuilder()
                        ->add('auteur',EntityType::class,[
                            'class' => Auteur::class,
                            'choice_label' => 'nom_prenom'
                        ])
                        ->add('note',IntegerType::class,[
                            'required'   => false,
                            'empty_data' => 1,
                            'constraints' => new Assert\Range(['min'=>1,'max'=>20]),
                        ])
                        ->add('envoyer',SubmitType::class)
                        ->getForm();
        $form->handleRequest($request);
        $new_note=$form->get("note")->getData();
        $auteur=$form->get("auteur")->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->getDoctrine()->getManager()->getRepository(Livre::class); 
            $livres = $auteur->getLivres();
            foreach($livres as $livre){
                $verif_note=$livre->getNote() + $new_note - 20;
                if($verif_note>0){
                    $new_note_verif = $new_note - $verif_note;
                    $repo->queryAction26($new_note_verif,$livre->getId());
                }
                else{
                    $repo->queryAction26($new_note,$livre->getId());
                }
            }
            $this->addFlash('notification', 'Les notes ont bien été augmentées !');
            return $this->redirect('/');
        }
        return $this->render('auteur/action26.html.twig', [
            'form26' => $form->createView()
        ]);
    }
}
