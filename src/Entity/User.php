<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{

    public function __toString()
    {
        return $this->getPrenom() . " ". $this->getNom();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message= "L'email ne peut être vide !")
     * Assert\Length(min="5",
     *     max="180",
     *     minMessage="5 caractères minimum !",
     *     maxMessage="180 caractères maximum !")
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(message= "Le pseudo ne peut être vide !")
     * Assert\Length(min="6",
     *     max="255",
     *     minMessage="6 caractères minimum !",
     *     maxMessage="255 caractères maximum !")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    /**
     * @Assert\NotBlank(message= "Le prénom ne peut être vide !")
     * Assert\Length(min="2",
     *     max="255",
     *     minMessage="2 caractères minimum !",
     *     maxMessage="255 caractères maximum !")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @Assert\NotBlank(message= "Le nom ne peut être vide !")
     * Assert\Length(min="2",
     *     max="255",
     *     minMessage="2 caractères minimum !",
     *     maxMessage="255 caractères maximum !")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message= "Le téléphone ne peut être vide !")
     * Assert\Length(min="12",
     *     max="12",
     *     exactMessage="12 caractères requis !")
     *
     * @ORM\Column(type="string", length=12)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoPath;

    /**
     * @Assert\NotBlank(message= "La ville ne peut être vide !")
     * Assert\Length(min="1",
     *     max="255",
     *     minMessage="1 caractères minimum !",
     *     maxMessage="255 caractères maximum !")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="organisateur")
     */
    private $sortiesOrg;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sortie", mappedBy="participants")
     */
    private $sorties;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userSite;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Groupe", mappedBy="chef", orphanRemoval=true)
     */
    private $groupes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Groupe", mappedBy="partipants")
     */
    private $groupesIncrit;



    public function __construct()
    {

        $this->sortiesOrg = new ArrayCollection();
        $this->sorties = new ArrayCollection();
        $this->setRoles(['ROLE_USER']);
        $this->setEtat(true);
        $this->groupes = new ArrayCollection();
        $this->groupesIncrit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(string $photoPath): self
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection|SortieOrg[]
     */
    public function getSortiesOrg(): Collection
    {
        return $this->sortiesOrg;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sortiesOrg->contains($sorty)) {
            $this->sortiesOrg[] = $sorty;
            $sorty->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sortiesOrg->contains($sorty)) {
            $this->sortiesOrg->removeElement($sorty);
            // set the owning side to null (unless already changed)
            if ($sorty->getOrganisateur() === $this) {
                $sorty->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

        public function getUserSite(): ?Site
    {
        return $this->userSite;
    }

    public function setUserSite(?Site $userSite): self
    {
        $this->userSite = $userSite;

        return $this;
    }

    public function getEtat(): ?bool
    {

        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->setChef($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->contains($groupe)) {
            $this->groupes->removeElement($groupe);
            // set the owning side to null (unless already changed)
            if ($groupe->getChef() === $this) {
                $groupe->setChef(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getGroupesIncrit(): Collection
    {
        return $this->groupesIncrit;
    }

    public function addGroupesIncrit(Groupe $groupesIncrit): self
    {
        if (!$this->groupesIncrit->contains($groupesIncrit)) {
            $this->groupesIncrit[] = $groupesIncrit;
            $groupesIncrit->addPartipant($this);
        }

        return $this;
    }

    public function removeGroupesIncrit(Groupe $groupesIncrit): self
    {
        if ($this->groupesIncrit->contains($groupesIncrit)) {
            $this->groupesIncrit->removeElement($groupesIncrit);
            $groupesIncrit->removePartipant($this);
        }

        return $this;
    }
}
