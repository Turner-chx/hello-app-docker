<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SavHistoryRepository")
 */
class SavHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sav", inversedBy="savHistories", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $sav;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $historyDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $event;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statusSetting;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHistoryDate(): ?\DateTimeInterface
    {
        return $this->historyDate;
    }

    public function setHistoryDate(?\DateTimeInterface $historyDate): self
    {
        $this->historyDate = $historyDate;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(?string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getStatusSetting(): ?string
    {
        return $this->statusSetting;
    }

    public function setStatusSetting(?string $statusSetting): self
    {
        $this->statusSetting = $statusSetting;

        return $this;
    }

    public function getSav(): ?Sav
    {
        return $this->sav;
    }

    public function setSav(?Sav $sav): self
    {
        $this->sav = $sav;

        return $this;
    }
}
