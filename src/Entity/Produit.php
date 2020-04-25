<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//for constraints
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @ORM\Column(type="string", length=255)
     *  @Assert\Length(
     *      min = 2,
     *      max = 30,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\Length(
     *      min = 2,
     *      max = 60,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $Description;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive
     * @Assert\NotNull
     */
    private $prix;

    /**
     * @ORM\Column(type="float")
     *  @Assert\Positive
     * @Assert\NotNull
     */
    private $stock;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ContenuPanier", mappedBy="produit", cascade={"remove"})
     */
    private $contenuPanier;

    public function __construct()
    {
        $this->contenuPanier = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?float
    {
        return $this->stock;
    }

    public function setStock(float $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|ContenuPanier[]
     */
    public function getContenuPanier(): Collection
    {
        return $this->contenuPanier;
    }

    public function addContenuPanier(ContenuPanier $contenuPanier): self
    {
        if (!$this->contenuPanier->contains($contenuPanier)) {
            $this->contenuPanier[] = $contenuPanier;
            $contenuPanier->setProduit($this);
        }

        return $this;
    }

    public function removeContenuPanier(ContenuPanier $contenuPanier): self
    {
        if ($this->contenuPanier->contains($contenuPanier)) {
            $this->contenuPanier->removeElement($contenuPanier);
            // set the owning side to null (unless already changed)
            if ($contenuPanier->getProduit() === $this) {
                $contenuPanier->setProduit(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getNom();
    }

}
