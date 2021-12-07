<?php

namespace App\Entity;

use App\Repository\GammeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=GammeRepository::class)
 */
class Gamme
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
    private $gamme;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"gamme"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="gamme")
     */
    private $articles;

    /**
     * @ORM\ManyToMany(targetEntity=Source::class, mappedBy="gammes")
     */
    private $sources;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->sources = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->gamme ?? 'Nouveau Boitage';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGamme(): ?string
    {
        return $this->gamme;
    }

    public function setGamme(?string $gamme): self
    {
        $this->gamme = $gamme;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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
            $article->setGamme($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getGamme() === $this) {
                $article->setGamme(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Source[]
     */
    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function addSource(Source $source): self
    {
        if (!$this->sources->contains($source)) {
            $this->sources[] = $source;
            $source->addGamme($this);
        }

        return $this;
    }

    public function removeSource(Source $source): self
    {
        if ($this->sources->removeElement($source)) {
            $source->removeGamme($this);
        }

        return $this;
    }
}
