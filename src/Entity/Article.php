<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $designationAbridged;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ean;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stock;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="articles")
     * @ORM\JoinColumn(nullable=true)
     */
    private $brand;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="ProductType", inversedBy="articles")
     * @ORM\JoinColumn(nullable=true)
     */
    private $productType;

    /**
     * @ORM\ManyToOne(targetEntity=Oem::class, inversedBy="articles")
     */
    private $oem;

    /**
     * @ORM\ManyToOne(targetEntity=Gamme::class, inversedBy="articles")
     */
    private $gamme;

    /**
     * @ORM\ManyToOne(targetEntity=Color::class, inversedBy="articles")
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity=SavArticle::class, mappedBy="article")
     */
    private $savArticles;

    /**
     * @ORM\ManyToMany(targetEntity=Sav::class, mappedBy="replacementArticles")
     */
    private $savs;

    public function __toString()
    {
        if (null === $this->getId()) {
            return 'app.entity.Article.new';
        }
        return (string)$this->getReference() . ' - ' . $this->getDesignation();
    }

    public function __construct()
    {
        $this->stock = 0;
        $this->status = false;
        $this->savArticles = new ArrayCollection();
        $this->savs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getDesignationAbridged(): ?string
    {
        return $this->designationAbridged;
    }

    public function setDesignationAbridged(?string $designationAbridged): self
    {
        $this->designationAbridged = $designationAbridged;

        return $this;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function setEan(?string $ean): self
    {
        $this->ean = $ean;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getProductType(): ?ProductType
    {
        return $this->productType;
    }

    public function setProductType(?ProductType $productType): self
    {
        $this->productType = $productType;

        return $this;
    }

    public function getOem(): ?Oem
    {
        return $this->oem;
    }

    public function setOem(?Oem $oem): self
    {
        $this->oem = $oem;

        return $this;
    }

    public function getGamme(): ?Gamme
    {
        return $this->gamme;
    }

    public function setGamme(?Gamme $gamme): self
    {
        $this->gamme = $gamme;

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection|SavArticle[]
     */
    public function getSavArticles(): Collection
    {
        return $this->savArticles;
    }

    public function addSavArticle(SavArticle $savArticle): self
    {
        if (!$this->savArticles->contains($savArticle)) {
            $this->savArticles[] = $savArticle;
            $savArticle->setArticle($this);
        }

        return $this;
    }

    public function removeSavArticle(SavArticle $savArticle): self
    {
        if ($this->savArticles->removeElement($savArticle)) {
            // set the owning side to null (unless already changed)
            if ($savArticle->getArticle() === $this) {
                $savArticle->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sav[]
     */
    public function getSavs(): Collection
    {
        return $this->savs;
    }

    public function addSav(Sav $sav): self
    {
        if (!$this->savs->contains($sav)) {
            $this->savs[] = $sav;
            $sav->addReplacementArticle($this);
        }

        return $this;
    }

    public function removeSav(Sav $sav): self
    {
        if ($this->savs->removeElement($sav)) {
            $sav->removeReplacementArticle($this);
        }

        return $this;
    }
}
