<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SourceRepository")
 */
class Source
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sav", mappedBy="source")
     */
    private $savs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity=Dealer::class)
     */
    private $dealer;

    /**
     * @ORM\ManyToMany(targetEntity=Gamme::class, inversedBy="sources")
     */
    private $gammes;

    /**
     * @ORM\ManyToOne(targetEntity=Files::class, cascade={"persist"})
     */
    private $image;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $defaultSource;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dealerEmail;

    /**
     * @ORM\ManyToMany(targetEntity=Email::class, inversedBy="sources", cascade={"persist"})
     */
    private $emails;

    public function __construct()
    {
        $this->savs = new ArrayCollection();
        $this->gammes = new ArrayCollection();
        $this->emails = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getColorHtml()
    {
        return '<div style="width: 40px; height: 40px; background: ' . $this->getColor() . ';"> </div>';
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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
            $sav->setSource($this);
        }

        return $this;
    }

    public function removeSav(Sav $sav): self
    {
        if ($this->savs->removeElement($sav)) {
            // set the owning side to null (unless already changed)
            if ($sav->getSource() === $this) {
                $sav->setSource(null);
            }
        }

        return $this;
    }

    public function getDealer(): ?Dealer
    {
        return $this->dealer;
    }

    public function setDealer(?Dealer $dealer): self
    {
        $this->dealer = $dealer;

        return $this;
    }

    /**
     * @return Collection|Gamme[]
     */
    public function getGammes(): Collection
    {
        return $this->gammes;
    }

    public function addGamme(Gamme $gamme): self
    {
        if (!$this->gammes->contains($gamme)) {
            $this->gammes[] = $gamme;
        }

        return $this;
    }

    public function removeGamme(Gamme $gamme): self
    {
        $this->gammes->removeElement($gamme);

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

    public function getDefaultSource(): ?bool
    {
        return $this->defaultSource;
    }

    public function setDefaultSource(?bool $defaultSource): self
    {
        $this->defaultSource = $defaultSource;

        return $this;
    }

    public function getDealerEmail(): ?string
    {
        return $this->dealerEmail;
    }

    public function setDealerEmail(?string $dealerEmail): self
    {
        $this->dealerEmail = $dealerEmail;

        return $this;
    }

    /**
     * @return Collection|Email[]
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(Email $email): self
    {
        if (!$this->emails->contains($email)) {
            $this->emails[] = $email;
        }

        return $this;
    }

    public function removeEmail(Email $email): self
    {
        $this->emails->removeElement($email);

        return $this;
    }
}
