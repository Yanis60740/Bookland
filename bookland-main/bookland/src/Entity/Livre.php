<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 * @UniqueEntity(fields={"isbn"}, message="Cet isbn existe déjà.")
 */
class Livre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $titre;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbpages;

    /**
     * @ORM\Column(type="date")
     */
    private $date_de_parution;

    /**
     * @ORM\Column(type="integer")
     */
    private $note;

    /**
     * @ORM\ManyToMany(targetEntity=Auteur::class, mappedBy="livres")
     */
    private $auteurs;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="livres")
     */
    private $genres;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
        $this->genres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNbpages(): ?int
    {
        return $this->nbpages;
    }

    public function setNbpages(int $nbpages): self
    {
        $this->nbpages = $nbpages;

        return $this;
    }

    public function getDateDeParution(): ?\DateTimeInterface
    {
        return $this->date_de_parution;
    }

    public function setDateDeParution(\DateTimeInterface $date_de_parution): self
    {
        $this->date_de_parution = $date_de_parution;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection|Auteur[]
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function addAuteur(Auteur $auteur): self
    {
        $this->auteurs[] = $auteur;
        $auteur->addLivre($this);
        return $this;
    }

    public function removeAuteur(Auteur $auteur): self
    {
        if ($this->auteurs->removeElement($auteur)) {
            $auteur->removeLivre($this);
        }

        return $this;
    }

    /**
     * @return Collection|genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }


    // ---------------------------------------------------------------------------------------------------------------------

    public function __toString()
    {
        return $this->titre;
    }

    // Action 9
    public function showDetails()
    {
        return $this->isbn + ", " + $this->titre + ", " + $this->nbpages + ", " + $this->date_de_parution + ", "
        + $this->note + ", " + $this->genres + ", " + $this->auteurs;  
    }


    //Vérification de l'Isbn
    private function validateIsbn() {
        $isbn = $this->isbn;
        $errors = array();
        
        $countNum = 0;
        for($i = 0; $i < strlen($isbn); $i++) {
            if(preg_match('/^[0-9]*$/', $isbn[$i])) {
                $countNum++;
            }
        }

        if(!preg_match('/[0-9]?-/', $isbn)) {
            array_push($errors, "L'isbn ne doit contenir que des chiffres et des tirets");
        } else {
            // L'isbn contient 13 chiffres
            if($countNum != 13) {
                array_push($errors, "L'isbn doit contenir 13 chiffres");
            } else {
                //4 Tirets
                $countHy = 0;
                for($i = 0; $i < strlen($isbn); $i++) {
                    if(preg_match('/-/', $isbn[$i])) {
                        $countHy++;
                    }
                }
                if($countHy != 4) {
                    array_push($errors, "L'isbn doit contenir 4 tirets séparants les groupes de chiffres");
                } else {
                    //Positionnement des tirets
                    if(preg_match('/--/', $isbn)) {
                        array_push($errors, "Le format de l'isbn est incorrect");
                    }
                    if($isbn[strlen($isbn)-1] == '-') {
                        array_push($errors, "Les tirets doivent séparer les groupes de chiffres");
                    }
                }

                //Commence par 978 ou 979
                $debut = $isbn[0].$isbn[1].$isbn[2];
                if($debut !== "979" && $debut !== "978") {
                    array_push($errors, "L'isbn doit commencer par 978 ou 979");
                }

                //Somme divisible par 10...
                $sommeX = 0;
                $sommeY = 0;
                $chars = array();
                for($i = 0; $i < strlen($isbn); $i++) {
                    if(preg_match('/[0-9]/', $isbn[$i])) {
                        array_push($chars, $isbn[$i]);
                    }
                }
                $chars = array_reverse($chars);
                for($i = 0; $i < count($chars); $i++) {
                    if(($i+1)%2 == 0) {
                        $sommeX = $sommeX + intval($chars[$i]);
                    } else {
                        $sommeY = $sommeY + intval($chars[$i]);
                    }
                }
                
                if((3*$sommeX + $sommeY)%10 != 0) {
                    array_push($errors, "L'isbn n'est pas valide (sommes non divisibles par 10)");
                }
            }
        }
        return $errors;
    }

    //Vérification de la note
    private function validateNote() {
        $note = $this->note;
        $error = "";

        if($note < 0 || $note > 20) {
            $error = "La note doit être comprise entre 0 et 20";
        }

        return $error;
    }

    //Vérifications des données entrées dans le formulaire
    public function validateForm() {
        $errors = array();

        //Isbn
        foreach($this->validateIsbn() as $error) {
            array_push($errors, $error);
        }

        //Note
        if($this->validateNote() != "") {
            array_push($errors, $this->validateNote());
        }

        return $errors;
    }
}
