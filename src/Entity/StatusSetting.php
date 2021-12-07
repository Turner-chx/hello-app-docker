<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusSettingRepository")
 */
class StatusSetting
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
    private $setting;

    /**
     * @ORM\Column(type="boolean", options={"default"=true}, nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sav", mappedBy="statusSetting")
     */
    private $savs;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $byDefault;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $over;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $displayDivaltoReplaceButton;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    public function __toString()
    {
        return $this->setting;
    }

    public function __construct()
    {
        $this->status = true;
        $this->byDefault = false;
        $this->over = false;
        $this->savs = new ArrayCollection();
    }

    public function getColorHtml()
    {
        return '<div style="width: 40px; height: 40px; background: ' . $this->getColor() . ';"> </div>';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSetting(): ?string
    {
        return $this->setting;
    }

    public function setSetting(?string $setting): self
    {
        $this->setting = $setting;

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

    public function getByDefault(): ?bool
    {
        return $this->byDefault;
    }

    public function setByDefault(?bool $byDefault): self
    {
        $this->byDefault = $byDefault;

        return $this;
    }

    public function getOver(): ?bool
    {
        return $this->over;
    }

    public function setOver(?bool $over): self
    {
        $this->over = $over;

        return $this;
    }

    public function getDisplayDivaltoReplaceButton(): ?bool
    {
        return $this->displayDivaltoReplaceButton;
    }

    public function setDisplayDivaltoReplaceButton(?bool $displayDivaltoReplaceButton): self
    {
        $this->displayDivaltoReplaceButton = $displayDivaltoReplaceButton;

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
            $sav->setStatusSetting($this);
        }

        return $this;
    }

    public function removeSav(Sav $sav): self
    {
        if ($this->savs->removeElement($sav)) {
            // set the owning side to null (unless already changed)
            if ($sav->getStatusSetting() === $this) {
                $sav->setStatusSetting(null);
            }
        }

        return $this;
    }
}
