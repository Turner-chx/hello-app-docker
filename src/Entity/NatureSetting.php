<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NatureSettingRepository")
 */
class NatureSetting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $setting;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=SavArticle::class, mappedBy="natureSettings")
     */
    private $savArticles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codeDivalto;

    public function __toString()
    {
        return $this->setting;
    }

    public function __construct()
    {
        $this->status = true;
        $this->savArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSetting(): ?string
    {
        return $this->setting;
    }

    public function setSetting(string $setting): self
    {
        $this->setting = $setting;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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
            $savArticle->addNatureSetting($this);
        }

        return $this;
    }

    public function removeSavArticle(SavArticle $savArticle): self
    {
        if ($this->savArticles->removeElement($savArticle)) {
            $savArticle->removeNatureSetting($this);
        }

        return $this;
    }

    public function getCodeDivalto(): ?string
    {
        return $this->codeDivalto;
    }

    public function setCodeDivalto(?string $codeDivalto): self
    {
        $this->codeDivalto = $codeDivalto;

        return $this;
    }
}
