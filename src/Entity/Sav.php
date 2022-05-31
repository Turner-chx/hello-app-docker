<?php

namespace App\Entity;

use App\Enum\ClientTypeEnum;
use App\Enum\OverStatusEnum;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SavRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Sav
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serialNumberCustomer;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $repairDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SavHistory", mappedBy="sav", cascade={"persist"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $savHistories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Messaging", mappedBy="sav", cascade={"persist"})
     * @ORM\OrderBy({"createdAt"="ASC"})
     */
    private $messagings;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="savs", cascade={"persist"})
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Files", inversedBy="savs", cascade={"persist"})
     * @ORM\JoinTable(name="sav_files_proof")
     */
    private $savFilesProof;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Source", inversedBy="savs")
     */
    private $source;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Dealer", inversedBy="savs")
     */
    private $dealer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StatusSetting", inversedBy="savs")
     */
    private $statusSetting;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $store;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isNew;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $newMessage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dealerReference;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $divaltoNumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $over;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $carrierCode;

    /**
     * @ORM\OneToMany(targetEntity=SavArticle::class, mappedBy="sav", cascade={"persist"})
     */
    private $savArticles;

    /**
     * @ORM\ManyToMany(targetEntity=Article::class, inversedBy="savs")
     * @ORM\JoinTable(name="replacement_article")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $replacementArticles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customerPrinter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $secretCode;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $jiraLink;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $emailSent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $family;

    public function __toString(): string
    {
        return $this->getId() . ' - ' . $this->getStatusSetting();
    }

    public function getSavArticleString()
    {
        $output = [];
        foreach ($this->getSavArticles() as $savArticle) {
            $article = $savArticle->getArticle();
            if (null !== $article) {
                $output[] = $article->getDesignation();
            } else {
                $output[] = $savArticle->getUnknownArticle();
            }
        }
        return implode(',', $output);
    }

    public function __construct()
    {
        $this->createdAt = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $this->savHistories = new ArrayCollection();
        $this->messagings = new ArrayCollection();
        $this->isNew = true;
        $this->newMessage = false;
        $this->over = false;
        $this->savArticles = new ArrayCollection();
        $this->replacementArticles = new ArrayCollection();
        $this->clientType = ClientTypeEnum::CUSTOMER;
        $this->savFilesProof = new ArrayCollection();
        $this->emailSent = false;
    }

    public function getDescriptionExport()
    {
        return str_replace([',', "\r", "\n", "\t", ';'], ' ', $this->getDescription());
    }

    public function getCreatedAtFrench(): string
    {
        return null !== $this->getCreatedAt() ? $this->getCreatedAt()->format('d/m/Y') : '';
    }

    public function getOverAtFrench(): string
    {
        return null !== $this->getOverAt() ? $this->getOverAt()->format('d/m/Y') : '';
    }


    public function getCustomerAddressPostCode(): string
    {
        $customer = $this->getCustomer();
        if (null === $customer) {
            return 'Inconnue';
        }
        $postCode = $customer->getPostalCode();
        return $postCode ?? 'Inconnue';
    }

    public function getCustomerAddressCity(): string
    {
        $customer = $this->getCustomer();
        if (null === $customer) {
            return 'Inconnue';
        }
        $city = $customer->getCity();
        return $city ?? 'Inconnue';
    }

    public function getCommentLight(): ?string
    {
        return strip_tags(str_replace([',', ';'], ' ', $this->getComment()));
    }

    public function isReplaced(): string
    {
        return null !== $this->getDivaltoNumber() ? 'Oui' : 'Non';
    }

    public function getNatureSettings(): ?string
    {
        $products = [];
        foreach ($this->getSavArticles() as $savArticle) {
            foreach ($savArticle->getNatureSettings() as $natureSetting) {
                $products[] = $natureSetting->getSetting();
            }
        }
        return implode('|', $products);
    }

    public function getSerialNumber2(): ?string
    {
        $products = [];
        foreach ($this->getSavArticles() as $savArticle) {
            $products[] = $savArticle->getSerialNumber2();
        }
        return implode('|', $products);
    }

    public function getSerialNumber1(): ?string
    {
        $products = [];
        foreach ($this->getSavArticles() as $savArticle) {
            $products[] = $savArticle->getSerialNumber();
        }
        return implode('|', $products);
    }

    public function getReplacementProduct(): ?string
    {
        $products = [];
        foreach ($this->getReplacementArticles() as $replacementArticle) {
            $products[] = $replacementArticle->getReference();
        }
        return implode('|', $products);
    }

    public function getReplacementProductName(): ?string
    {
        $products = [];
        foreach ($this->getReplacementArticles() as $replacementArticle) {
            $products[] = $replacementArticle->getDesignation();
        }
        return implode('|', $products);
    }

    public function getOverAt(): ?DateTime
    {
        if ($this->isOver()) {
            return $this->updatedAt;
        }
        return null;
    }

    public function getClientTypeEnum(): ?string
    {
        return ClientTypeEnum::get($this->clientType);
    }

    public function isOver(): bool
    {
        $status = $this->getStatusSetting();
        if (null !== $status) {
            return $status->getOver();
        }
        return false;
    }

    public function isOverEnum(): ?string
    {
        if ($this->over) {
            return OverStatusEnum::get(OverStatusEnum::OVER);
        }
        return OverStatusEnum::get(OverStatusEnum::OPEN);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist(): void
    {
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getClientType(): ?string
    {
        return $this->clientType;
    }

    public function setClientType(string $clientType): self
    {
        $this->clientType = $clientType;

        return $this;
    }

    public function getStore(): ?string
    {
        return $this->store;
    }

    public function setStore(?string $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getIsNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(?bool $isNew): self
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function getNewMessage(): ?bool
    {
        return $this->newMessage;
    }

    public function setNewMessage(?bool $newMessage): self
    {
        $this->newMessage = $newMessage;

        return $this;
    }

    public function getDealerReference(): ?string
    {
        return $this->dealerReference;
    }

    public function setDealerReference(?string $dealerReference): self
    {
        $this->dealerReference = $dealerReference;

        return $this;
    }

    public function getDivaltoNumber(): ?string
    {
        return $this->divaltoNumber;
    }

    public function setDivaltoNumber(?string $divaltoNumber): self
    {
        $this->divaltoNumber = $divaltoNumber;

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

    public function getCarrierCode(): ?string
    {
        return $this->carrierCode;
    }

    public function setCarrierCode(?string $carrierCode): self
    {
        $this->carrierCode = $carrierCode;

        return $this;
    }

    /**
     * @return Collection|SavHistory[]
     */
    public function getSavHistories(): Collection
    {
        return $this->savHistories;
    }

    public function addSavHistory(SavHistory $savHistory): self
    {
        if (!$this->savHistories->contains($savHistory)) {
            $this->savHistories[] = $savHistory;
            $savHistory->setSav($this);
        }

        return $this;
    }

    public function removeSavHistory(SavHistory $savHistory): self
    {
        if ($this->savHistories->removeElement($savHistory)) {
            // set the owning side to null (unless already changed)
            if ($savHistory->getSav() === $this) {
                $savHistory->setSav(null);
            }
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
            $messaging->setSav($this);
        }

        return $this;
    }

    public function removeMessaging(Messaging $messaging): self
    {
        if ($this->messagings->removeElement($messaging)) {
            // set the owning side to null (unless already changed)
            if ($messaging->getSav() === $this) {
                $messaging->setSav(null);
            }
        }

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

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

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): self
    {
        $this->source = $source;

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

    public function getStatusSetting(): ?StatusSetting
    {
        return $this->statusSetting;
    }

    public function setStatusSetting(?StatusSetting $statusSetting): self
    {
        $this->statusSetting = $statusSetting;

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
            $savArticle->setSav($this);
        }

        return $this;
    }

    public function removeSavArticle(SavArticle $savArticle): self
    {
        if ($this->savArticles->removeElement($savArticle)) {
            // set the owning side to null (unless already changed)
            if ($savArticle->getSav() === $this) {
                $savArticle->setSav(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getReplacementArticles(): Collection
    {
        return $this->replacementArticles;
    }

    public function addReplacementArticle(Article $replacementArticle): self
    {
        if (!$this->replacementArticles->contains($replacementArticle)) {
            $this->replacementArticles[] = $replacementArticle;
        }

        return $this;
    }

    public function removeReplacementArticle(Article $replacementArticle): self
    {
        $this->replacementArticles->removeElement($replacementArticle);

        return $this;
    }

    public function getCustomerPrinter(): ?string
    {
        return $this->customerPrinter;
    }

    public function setCustomerPrinter(?string $customerPrinter): self
    {
        $this->customerPrinter = $customerPrinter;

        return $this;
    }

    public function getJiraLink(): ?string
    {
        return $this->jiraLink;
    }

    public function setJiraLink(?string $jiraLink): self
    {
        $this->jiraLink = $jiraLink;

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getSavFilesProof(): Collection
    {
        return $this->savFilesProof;
    }

    public function addSavFilesProof($savFilesProof): self
    {
        if (!$this->savFilesProof->contains($savFilesProof)) {
            $this->savFilesProof[] = $savFilesProof;
        }

        return $this;
    }

    public function removeSavFilesProof($savFilesProof): self
    {
        $this->savFilesProof->removeElement($savFilesProof);

        return $this;
    }

    public function getSerialNumberCustomer(): ?string
    {
        return $this->serialNumberCustomer;
    }

    public function setSerialNumberCustomer(?string $serialNumberCustomer): self
    {
        $this->serialNumberCustomer = $serialNumberCustomer;

        return $this;
    }

    public function getRepairDate(): ?\DateTimeInterface
    {
        return $this->repairDate;
    }

    public function setRepairDate(?\DateTimeInterface $repairDate): self
    {
        $this->repairDate = $repairDate;

        return $this;
    }

    public function getSecretCode(): ?string
    {
        return $this->secretCode;
    }

    public function setSecretCode(?string $secretCode): self
    {
        $this->secretCode = $secretCode;

        return $this;
    }

    public function getEmailSent(): ?bool
    {
        return $this->emailSent;
    }

    public function setEmailSent(?bool $emailSent): self
    {
        $this->emailSent = $emailSent;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(?string $family): self
    {
        $this->family = $family;

        return $this;
    }
}

