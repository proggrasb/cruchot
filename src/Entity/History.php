<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotNull]
    private ?int $rateId = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotNull]
    private ?int $currencyId = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    #[Assert\Length(min: 1, max: 32)]
    private ?string $valueRate = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    #[Assert\Length(min: 1, max: 32)]
    private ?string $unitRate = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotNull]
    private ?int $nominal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRateId(): ?int
    {
        return $this->rateId;
    }

    public function setRateId(?int $rateId): self
    {
        $this->rateId = $rateId;
        return $this;
    }

    public function getCurrencyId(): ?int
    {
        return $this->currencyId;
    }

    public function setCurrencyId(?int $currencyId): self
    {
        $this->currencyId = $currencyId;
        return $this;
    }

    public function getValueRate(): ?string
    {
        return $this->valueRate;
    }

    public function setValueRate(?string $valueRate): self
    {
        $this->valueRate = $valueRate;
        return $this;
    }

    public function getUnitRate(): ?string
    {
        return $this->unitRate;
    }

    public function setUnitRate(?string $unitRate): self
    {
        $this->unitRate = $unitRate;
        return $this;
    }

    public function getNominal(): ?int
    {
        return $this->nominal;
    }

    public function setNominal(?int $nominal): self
    {
        $this->nominal = $nominal;
        return $this;
    }
}
