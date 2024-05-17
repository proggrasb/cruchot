<?php

namespace App\Entity;

use App\Repository\RatesRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatesRepository::class)]
class Rates
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $ratedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRatedAt(): ?DateTime
    {
        return $this->ratedAt;
    }

    public function setRatedAt(?DateTime $ratedAt): self
    {
        $this->ratedAt = $ratedAt;
        return $this;
    }
}
