<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Entity\Auteur;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/livre")
 */
class LivreController extends AbstractController
{
    /**
     * @Route("/", name="livre_index", methods={"GET"})
     */
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="livre_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        $given_errors = $livre->validateForm();
        $errors = array();

        if ($form->isSubmitted() && $form->isValid()) {
            if(empty($given_errors)) {
                if(isset($request->request->get('livre')['auteurs'])) {
                    foreach($request->request->get('livre')['auteurs'] as $id) {
                        $auteur = $entityManager->getRepository(Auteur::class)->find($id);
                        $livre->addAuteur($auteur);
                    }
                }
                $entityManager->persist($livre);
                $entityManager->flush();
    
                return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
            } else {
                foreach($given_errors as $error) {
                    array_push($errors, $error);
                }
            }
        } else if($form->isSubmitted() && !$form->isValid()) {
            array_push($errors, $form['isbn']->getErrors());
        }

        return $this->render('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/{id}/show", name="livre_show", methods={"GET"})
     */
    public function show(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre
        ]);
    }

    /**
     * @Route("/{id}/edit", name="livre_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        $given_errors = $livre->validateForm();
        $errors = array();

        if ($form->isSubmitted() && $form->isValid()) {
            if(empty($given_errors)) {
                if(isset($request->request->get('livre')['auteurs'])) {
                    foreach($request->request->get('livre')['auteurs'] as $id) {
                        $auteur = $entityManager->getRepository(Auteur::class)->find($id);
                        $livre->addAuteur($auteur);
                    }
                }
                $entityManager->flush();
    
                return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
            } else {
                foreach($given_errors as $error) {
                    array_push($errors, $error);
                }
            }
        } else if($form->isSubmitted() && !$form->isValid()) {
            array_push($errors, $form['isbn']->getErrors());
        }

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/{id}/delete", name="livre_delete", methods={"POST"})
     */
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
    }

    

    // FAIT PAR YANIS

    /**
     * @Route("/action13", name="livre_actions13", methods={"GET", "POST"})
     */    
    public function action13(Request $request)
    {
        $form = $this->createFormBuilder()
                        ->add('date_de_debut',DateType::class, [
                            'widget' => 'single_text'
                        ])
                        ->add('date_de_fin', DateType::class, [
                            'widget' => 'single_text'
                        ])
                        ->add('envoyer',SubmitType::class)
                        ->getForm();
        
        $form->handleRequest($request);
        $date1=$form->get("date_de_debut")->getData();
        $date2=$form->get("date_de_fin")->getData();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->getDoctrine()->getManager()->getRepository(Livre::class); 
            $livre = $repo->queryAction13($date1,$date2);
            return $this->render('livre/action13_resultat.html.twig', [
                'action13' => $livre
            ]);
        }
        return $this->render('livre/action13.html.twig', [
            'form13' => $form->createView()
        ]);
    }

    /**
     * @Route("/action15", name="livre_actions15", methods={"GET", "POST"})
     */
    public function action15(Request $request)
    {   
        $form = $this->createFormBuilder()
            ->add('date_de_debut',DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('date_de_fin', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('note_inf',IntegerType::class,[ 'constraints' => new Assert\Range(['min'=>0,'max'=>20])])
            ->add('note_sup',IntegerType::class,[ 'constraints' => new Assert\Range(['min'=>0,'max'=>20])])
            ->add('envoyer',SubmitType::class)
            ->getForm();
        
        $form->handleRequest($request);
        $date1=$form->get("date_de_debut")->getData();
        $date2=$form->get("date_de_fin")->getData();
        $note1=$form->get("note_inf")->getData();
        $note2=$form->get("note_sup")->getData();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->getDoctrine()->getManager()->getRepository(Livre::class); 
            $livre = $repo->queryAction15($date1,$date2,$note1,$note2);
            return $this->render('livre/action15_resultat.html.twig', [
                'action15' => $livre
            ]);
        }
        return $this->render('livre/action15.html.twig', [
            'form15' => $form->createView()
        ]);
    }

    /**
     * @Route("/action25", name="livre_actions25", methods={"GET", "POST"})
     */
    public function action25(Request $request)
    {
        $form = $this->createFormBuilder()
                        ->add('titre',TextType::class)
                        ->add('envoyer',SubmitType::class)
                        ->getForm();
        $form->handleRequest($request);
        $titre=$form->get("titre")->getData();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->getDoctrine()->getManager()->getRepository(Livre::class); 
            $recherche = $repo->queryAction25($titre);
            return $this->render('livre/action25_resultat.html.twig', [
                'action25' => $recherche,
            ]);
        }
        return $this->render('livre/action25.html.twig', [
            'form25' => $form->createView()
        ]);
    }


    //FAIT PAR AXEL

    /**
     * @Route("/actionsL", name="livre_actions", methods={"GET","POST"})
     */
    public function actionsL(Request $request, LivreRepository $livreRepository)
    {
        $action17 = $this->action17($livreRepository);
        $action14 = $this->action14($livreRepository);

        return $this->render('livre/actionsL.html.twig', [
            'action14' => $action14,
            'action17' => $action17,
            ]);     
    }

    //Action Livre
    private function action14(LivreRepository $livreRepository)
    {
       $livres = $livreRepository->findAll();
       $livreAutNatioDiff = [];

      foreach($livres as $livre)
      {
       $auteurs = $livre->getAuteurs();
       $NationaliteDiff = [];
       $cpt = 0;
       foreach($auteurs as $auteur)
       {
        $test=in_array($auteur->getNationalite(),$NationaliteDiff);
        if($test==false)
        {
         $NationaliteDiff[]=$auteur->getNationalite();
        }
        $cpt++;
       }
       if($cpt==count($NationaliteDiff))
       {
        $livreAutNatioDiff[]=$livre;
       }
      }
       return $livreAutNatioDiff;
    }

    private function action17(LivreRepository $livreRepository)
    {
       $livres = $livreRepository->findAll();
       $livreAvecParite = [];
       foreach($livres as $livre)
       {
         $cptH = 0;
         $cptF = 0;
         $auteurs = $livre->getAuteurs();
         $nbauteurs = count($livre->getAuteurs());

         foreach($auteurs as $auteur)
         {

           if(($nbauteurs % 2 == 0) && ($nbauteurs > 1))
            {
              $Sexe = $auteur->getSexe();
              if($Sexe == "M")
               {
                 $cptH++;
               }
              if($Sexe == "F")
               {
                 $cptF++;
               }
           }
           else
            {
             $cptH=3;
             $cptF=0;
            }
            
         }
           if($cptH=$cptF)
            {
              $livreAvecParite[] = $livre;
            }            
        }
      return $livreAvecParite;
    }
}
