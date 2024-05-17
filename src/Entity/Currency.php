<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
#[ORM\Table(name: 'currency')]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: 'string', length: 3, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 3)]
    #[Assert\NotNull]
    private ?string $code = null;

    #[ORM\Column(type: 'string', length: 3, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 3)]
    #[Assert\NotNull]
    private ?string $scode = null;

    #[ORM\Column(type: 'string', length: 128, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 128)]
    private ?string $name = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getScode(): ?string
    {
        return $this->scode;
    }

    public function setScode(string $scode): self
    {
        $this->scode = $scode;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
