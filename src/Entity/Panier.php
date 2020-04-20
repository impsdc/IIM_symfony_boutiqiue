<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PanierRepository")
 */
class Panier
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\JoinColumn(nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="paniers")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ContenuPanier", mappedBy="panier")
     */
    private $contenuPaniers;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->contenuPaniers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|ContenuPanier[]
     */
    public function getContenuPaniers(): Collection
    {
        return $this->contenuPaniers;
    }

    public function addContenuPanier(ContenuPanier $contenuPanier): self
    {
        if (!$this->contenuPaniers->contains($contenuPanier)) {
            $this->contenuPaniers[] = $contenuPanier;
            $contenuPanier->setPanier($this);
        }

        return $this;
    }

    public function removeContenuPanier(ContenuPanier $contenuPanier): self
    {
        if ($this->contenuPaniers->contains($contenuPanier)) {
            $this->contenuPaniers->removeElement($contenuPanier);
            // set the owning side to null (unless already changed)
            if ($contenuPanier->getPanier() === $this) {
                $contenuPanier->setPanier(null);
            }
        }

        return $this;
    }
}
