<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\Auteur;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main", methods={"GET"})
    */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }


    //FAIT PAR AXEL

    /**
     * @Route("/init", name="init", methods={"GET"})
     * @return Response
     */
    public function init(EntityManagerInterface $entityManager): Response
    {
        // On vide la base pour éviter les doublons (méthode un peu brutale)

        $repo = $this->getDoctrine()->getManager()->getRepository(Genre::class);
        $genres = $repo->findAll();
        foreach($genres as $genre) {
            $entityManager->remove($genre);
            $entityManager->flush();   
        }

        $repo2 = $this->getDoctrine()->getManager()->getRepository(Livre::class);
        $livres = $repo2->findAll();
        foreach($livres as $livre) {
            $entityManager->remove($livre);
            $entityManager->flush();   
        }

        $repo3 = $this->getDoctrine()->getManager()->getRepository(Auteur::class);
        $auteurs = $repo3->findAll();
        foreach($auteurs as $auteur) {
            $entityManager->remove($auteur);
            $entityManager->flush();   
        }

        $genre = new Genre();
	$genre->setNom('science fiction');

        $genre2 = new Genre();
        $genre2->setNom('policier');

        $genre3 = new Genre();
        $genre3->setNom('philosophie');

        $genre4 = new Genre();
        $genre4->setNom('économie');

        $genre5 = new Genre();
        $genre5->setNom('psychologie');

        $auteur = new Auteur();
        $auteur->setNomPrenom('Richard Taleur');
        $auteur->setSexe('H');
        $auteur->setDateDeNaissance(new \DateTime('12-12-1945'));
        $auteur->setNationalite('USA');

        $auteur2 = new Auteur();
        $auteur2->setNomPrenom('Cass Sunstein');
        $auteur2->setSexe('H');
        $auteur2->setDateDeNaissance(new \DateTime('23-11-1943'));
        $auteur2->setNationalite('Allemagne');

        $auteur3 = new Auteur();
        $auteur3->setNomPrenom('Francis Gabrelot');
        $auteur3->setSexe('H');
        $auteur3->setDateDeNaissance(new \DateTime('29-01-1967'));
        $auteur3->setNationalite('France');

        $auteur4 = new Auteur();
        $auteur4->setNomPrenom('Ayn Rand');
        $auteur4->setSexe('F');
        $auteur4->setDateDeNaissance(new \DateTime('21-06-1950'));
        $auteur4->setNationalite('Russie');

        $auteur5 = new Auteur();
        $auteur5->setNomPrenom('Duschmol');
        $auteur5->setSexe('H');
        $auteur5->setDateDeNaissance(new \DateTime('23-12-2001'));
        $auteur5->setNationalite('Groeland');

        $auteur6 = new Auteur();
        $auteur6->setNomPrenom('Nancy Grave');
        $auteur6->setSexe('F');
        $auteur6->setDateDeNaissance(new \DateTime('24-10-1952'));
        $auteur6->setNationalite('USA');

        $auteur7 = new Auteur();
        $auteur7->setNomPrenom('James Enckling');
        $auteur7->setSexe('H');
        $auteur7->setDateDeNaissance(new \DateTime('03-07-1970'));
        $auteur7->setNationalite('USA');

        $auteur8 = new Auteur();
        $auteur8->setNomPrenom('Jean Dupont');
        $auteur8->setSexe('H');
        $auteur8->setDateDeNaissance(new \DateTime('03-07-1970'));
        $auteur8->setNationalite('France');

        $livre = new Livre();
        $livre->setIsbn('978-2-07-036822-8');
        $livre->setTitre('Symfonistique');
        $livre->setNbpages('117');
        $livre->setDateDeParution(new \DateTime('20-01-2008'));
        $livre->setNote('8');
        $livre->addGenre($genre2);
        $livre->addGenre($genre3);
        $livre->addAuteur($auteur3);
        $livre->addAuteur($auteur6);
        $livre->addAuteur($auteur4);

        $livre2 = new Livre();
        $livre2->setIsbn('978-2-251-44417-8');
        $livre2->setTitre('La grève');
        $livre2->setNbpages('1245');
        $livre2->setDateDeParution(new \DateTime('12-06-1961'));
        $livre2->setNote('19');
        $livre2->addGenre($genre3);
        $livre2->addAuteur($auteur4);
        $livre2->addAuteur($auteur7);

        $livre3 = new Livre();
        $livre3->setIsbn('978-2-212-55652-0');
        $livre3->setTitre('Symfonyland');
        $livre3->setNbpages('131');
        $livre3->setDateDeParution(new \DateTime('17-09-1980'));
        $livre3->setNote('15');
        $livre3->addGenre($genre);
        $livre3->addAuteur($auteur7);
        $livre3->addAuteur($auteur8);
        $livre3->addAuteur($auteur4);

        $livre4 = new Livre();
        $livre4->setIsbn('978-2-0807-1057-0');
        $livre4->setTitre('Négociation Complexe');
        $livre4->setNbpages('234');
        $livre4->setDateDeParution(new \DateTime('25-09-1992'));
        $livre4->setNote('16');
        $livre4->addGenre($genre5);
        $livre4->addAuteur($auteur);
        $livre4->addAuteur($auteur2);

        $livre5 = new Livre();
        $livre5->setIsbn('978-0-300-12223-7');
        $livre5->setTitre('Ma vie');
        $livre5->setNbpages('5');
        $livre5->setDateDeParution(new \DateTime('08-11-2021'));
        $livre5->setNote('03');
        $livre5->addGenre($genre2);
        $livre5->addAuteur($auteur8);

        $livre6 = new Livre();
        $livre6->setIsbn('978-0-141-18776-1');
        $livre6->setTitre('Ma vie : suite');
        $livre6->setNbpages('5');
        $livre6->setDateDeParution(new \DateTime('09-11-2021'));
        $livre6->setNote('01');
        $livre6->addGenre($genre2);
        $livre6->addAuteur($auteur8);

        $livre7 = new Livre();
        $livre7->setIsbn('978-0-141-18786-0');
        $livre7->setTitre('Le monde comme volonté et comme représentation');
        $livre7->setNbpages('1987');
        $livre7->setDateDeParution(new \DateTime('09-11-1821'));
        $livre7->setNote('19');
        $livre7->addGenre($genre3);
        $livre7->addAuteur($auteur3);
        $livre7->addAuteur($auteur6);

        $em = $entityManager;
        $em->persist($genre);
        $em->persist($genre2);
        $em->persist($genre3);
        $em->persist($genre4);
        $em->persist($genre5);
        $em->persist($auteur);
        $em->persist($auteur2);
        $em->persist($auteur3);
        $em->persist($auteur4);
        $em->persist($auteur5);
        $em->persist($auteur6);
        $em->persist($auteur7);
        $em->persist($auteur8);
        $em->persist($livre);
        $em->persist($livre2);
        $em->persist($livre3);
        $em->persist($livre4);
        $em->persist($livre5);
        $em->persist($livre6);
        $em->persist($livre7);

        $em->flush();
        $this->addFlash('notification', 'Le site a bien été initialisé !');
        return $this->redirect('/');
    }
}
