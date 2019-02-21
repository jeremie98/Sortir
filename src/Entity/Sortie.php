<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message= "Le nom ne peut être vide !")
     * @Assert\Length(min="5",
     *     max="100",
     *     minMessage="5 caractères minimum !",
     *     maxMessage="100 caractères maximum !")
     *
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message="La date ne peut être vide !")
     * @Assert\GreaterThan("today")
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateSortie;

    /**
     * @Assert\NotBlank(message="La date ne peut être vide")
     * @Assert\GreaterThan("today")
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateLimiteInscription;

    /**
     * @Assert\NotBlank(message="Veuillez renseigner un nombre de place !")
     * @Assert\Range(min="2",
     *     max="500",
     *     minMessage="2 participants minimum !",
     *     maxMessage="500 participants maximum !"
     *     )
     * @ORM\Column(type="integer", nullable=false)
     */
    private $nbPlace;

    /**
     * @Assert\NotBlank(message="Veuillez renseigner une durée !")
     * @Assert\Range(min="30",
     *     minMessage="30 minutes minimum !"
     *     )
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $duree;

    /**
     * @Assert\NotBlank(message="Veuillez renseigner une description !")
     * @Assert\Length(min="10",
     *     max="500",
     *     minMessage="10 caractères minimum !",
     *     maxMessage="60000 caractères maximum !"
     *     )
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="sorties")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $siteOrg;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Annulation", mappedBy="sortie", cascade={"persist", "remove"})
     */
    private $annulation;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->dateLimiteInscription = new \DateTime();
        $this->dateSortie = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(?\DateTimeInterface $dateSortie): self
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): self
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }

        return $this;
    }

    public function getSiteOrg(): ?Site
    {
        return $this->siteOrg;
    }

    public function setSiteOrg(?Site $siteOrg): self
    {
        $this->siteOrg = $siteOrg;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getAnnulation(): ?Annulation
    {
        return $this->annulation;
    }

    public function setAnnulation(?Annulation $annulation): self
    {
        $this->annulation = $annulation;

        // set (or unset) the owning side of the relation if necessary
        $newSortie = $annulation === null ? null : $this;
        if ($newSortie !== $annulation->getSortie()) {
            $annulation->setSortie($newSortie);
        }

        return $this;
    }
}
