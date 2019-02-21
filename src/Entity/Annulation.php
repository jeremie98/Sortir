<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AnnulationRepository")
 */
class Annulation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Sortie", inversedBy="annulation", cascade={"persist", "remove"})
     */
    private $sortie;

    /**
     * @Assert\NotBlank(message="Le motif ne peut être nul ! ")
     * @Assert\Length(min="10",
     *     max="255",
     *     minMessage="10 caractères minimum !",
     *     maxMessage="255 caractères maximum !")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $motif;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSortie(): ?Sortie
    {
        return $this->sortie;
    }

    public function setSortie(?Sortie $sortie): self
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }
}
