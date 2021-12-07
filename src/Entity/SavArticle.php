<?php

namespace App\Entity;

use App\Repository\SavArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SavArticleRepository::class)
 */
class SavArticle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sav::class, inversedBy="savArticles")
     */
    private $sav;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="savArticles")
     */
    private $article;

    /**
     * @ORM\ManyToMany(targetEntity=Color::class, inversedBy="savArticles")
     */
    private $colors;

    /**
     * @ORM\ManyToMany(targetEntity=NatureSetting::class, inversedBy="savArticles")
     */
    private $natureSettings;

    /**
     * @ORM\ManyToOne(targetEntity=Files::class, cascade={"persist"}, inversedBy="savArticles")
     */
    private $fileUnknown;

    /**
     * @ORM\ManyToMany(targetEntity=Files::class, inversedBy="savArticlesProof")
     */
    private $filesProof;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $unknownArticle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serialNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serialNumber2;

    public function __toString()
    {
        $article = $this->getArticle();
        if (null !== $article) {
            return $article->getDesignation() ?? (string)$this->getId();
        }
        return (string)$this->getId();
    }

    public function getDisplayNatureSetting(): ?string
    {
        $natureSettings = $this->getNatureSettings();
        $string = '';
        $i = 0;
        foreach ($natureSettings as $natureSetting) {
            if ($natureSetting->getStatus()){
                if ($i > 0) {
                    $string .= '<br>' . '- ' . $natureSetting->getSetting();
                } else {
                    $string .= '- ' . $natureSetting->getSetting();
                }
                $i++;
            }
        }
        return $string;
    }

    public function __construct()
    {
        $this->colors = new ArrayCollection();
        $this->natureSettings = new ArrayCollection();
        $this->filesProof = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return Collection|Color[]
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function addColor(Color $color): self
    {
        if (!$this->colors->contains($color)) {
            $this->colors[] = $color;
        }

        return $this;
    }

    public function removeColor(Color $color): self
    {
        $this->colors->removeElement($color);

        return $this;
    }

    /**
     * @return Collection|NatureSetting[]
     */
    public function getNatureSettings(): Collection
    {
        return $this->natureSettings;
    }

    public function addNatureSetting(NatureSetting $natureSetting): self
    {
        if (!$this->natureSettings->contains($natureSetting)) {
            $this->natureSettings[] = $natureSetting;
        }

        return $this;
    }

    public function removeNatureSetting(NatureSetting $natureSetting): self
    {
        $this->natureSettings->removeElement($natureSetting);

        return $this;
    }

    public function getFileUnknown()
    {
        return $this->fileUnknown;
    }

    public function setFileUnknown($fileUnknown): self
    {
        $this->fileUnknown = $fileUnknown;

        return $this;
    }

    /**
     * @return Collection|Files[]
     */
    public function getFilesProof(): Collection
    {
        return $this->filesProof;
    }

    public function addFilesProof($filesProof): self
    {
        if (!$this->filesProof->contains($filesProof)) {
            $this->filesProof[] = $filesProof;
        }

        return $this;
    }

    public function removeFilesProof($filesProof): self
    {
        $this->filesProof->removeElement($filesProof);

        return $this;
    }

    public function getUnknownArticle(): ?string
    {
        return $this->unknownArticle;
    }

    public function setUnknownArticle(?string $unknownArticle): self
    {
        $this->unknownArticle = $unknownArticle;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getSerialNumber2(): ?string
    {
        return $this->serialNumber2;
    }

    public function setSerialNumber2(?string $serialNumber2): self
    {
        $this->serialNumber2 = $serialNumber2;

        return $this;
    }
}
