<?php

namespace App\Entity;

use App\Enum\SenderFileEnum;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;


/**
 * @ORM\Entity(repositoryClass="App\Repository\FilesRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class Files
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
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $updatedAt;

    /**
     * @Vich\UploadableField(mapping="uploaded_files", fileNameProperty="name")
     * @var File
     */
    private $file;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sav", mappedBy="savFilesProof")
     */
    private $savs;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Messaging", mappedBy="files")
     */
    private $messagings;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sender;

    /**
     * @ORM\OneToMany(targetEntity=SavArticle::class, mappedBy="fileUnknown")
     */
    private $savArticles;

    /**
     * @ORM\ManyToMany(targetEntity=SavArticle::class, mappedBy="filesProof")
     */
    private $savArticlesProof;

    public function __toString()
    {
        if (null !== $this->name) {
            return (string)$this->name;
        }
        return '';
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist(): void
    {
        $this->updatedAt = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
    }

    public function __construct()
    {
        $this->createdAt = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $this->savs = new ArrayCollection();
        $this->messagings = new ArrayCollection();
        $this->sender = SenderFileEnum::LAMA;
        $this->savArticles = new ArrayCollection();
        $this->savArticlesProof = new ArrayCollection();
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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $name = null): self
    {
        $this->file = $name;
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($name) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        }

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
            $sav->addFile($this);
        }

        return $this;
    }

    public function removeSav(Sav $sav): self
    {
        if ($this->savs->contains($sav)) {
            $this->savs->removeElement($sav);
            $sav->removeFile($this);
        }

        return $this;
    }

    /**
     * @return Collection|Messaging[]
     */
    public function getMessagings(): Collection
    {
        return $this->messagings;
    }

    public function addMessaging(Messaging $messaging): self
    {
        if (!$this->messagings->contains($messaging)) {
            $this->messagings[] = $messaging;
            $messaging->addFile($this);
        }

        return $this;
    }

    public function removeMessaging(Messaging $messaging): self
    {
        if ($this->messagings->contains($messaging)) {
            $this->messagings->removeElement($messaging);
            $messaging->removeFile($this);
        }

        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(?string $sender): self
    {
        $this->sender = $sender;

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
            $savArticle->setFileUnknown($this);
        }

        return $this;
    }

    public function removeSavArticle(SavArticle $savArticle): self
    {
        if ($this->savArticles->removeElement($savArticle)) {
            // set the owning side to null (unless already changed)
            if ($savArticle->getFileUnknown() === $this) {
                $savArticle->setFileUnknown(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SavArticle[]
     */
    public function getSavArticlesProof(): Collection
    {
        return $this->savArticlesProof;
    }

    public function addSavArticlesProof(SavArticle $savArticlesProof): self
    {
        if (!$this->savArticlesProof->contains($savArticlesProof)) {
            $this->savArticlesProof[] = $savArticlesProof;
            $savArticlesProof->addFilesProof($this);
        }

        return $this;
    }

    public function removeSavArticlesProof(SavArticle $savArticlesProof): self
    {
        if ($this->savArticlesProof->removeElement($savArticlesProof)) {
            $savArticlesProof->removeFilesProof($this);
        }

        return $this;
    }
}
