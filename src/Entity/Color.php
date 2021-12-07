<?php

namespace App\Entity;

use App\Repository\ColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ColorRepository::class)
 */
class Color
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idLama;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Files", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    private $image;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPack;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="color")
     */
    private $articles;

    /**
     * @ORM\ManyToMany(targetEntity=SavArticle::class, mappedBy="colors")
     */
    private $savArticles;

    public function __construct()
    {
        $this->isPack = false;
        $this->articles = new ArrayCollection();
        $this->savArticles = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getColor();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdLama(): ?string
    {
        return $this->idLama;
    }

    public function setIdLama(?string $idLama): self
    {
        $this->idLama = $idLama;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getIsPack(): ?bool
    {
        return $this->isPack;
    }

    public function setIsPack(?bool $isPack): self
    {
        $this->isPack = $isPack;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?Files
    {
        return $this->image;
    }

    public function setImage(?Files $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setColor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getColor() === $this) {
                $article->setColor(null);
            }
        }

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
            $savArticle->addColor($this);
        }

        return $this;
    }

    public function removeSavArticle(SavArticle $savArticle): self
    {
        if ($this->savArticles->removeElement($savArticle)) {
            $savArticle->removeColor($this);
        }

        return $this;
    }
}
