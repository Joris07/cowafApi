<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user', 'facture'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le montant ne peut pas être nul.")]
    #[Assert\GreaterThanOrEqual(value: 0, message: "Le montant doit être supérieur ou égal à zéro.")]
    #[Groups(['facture'])]
    private ?int $montant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['facture'])]
    private ?\DateTime $date = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['facture'])]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'facture')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['facture'])]
    private ?Trajet $trajet = null;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTrajet(): ?Trajet
    {
        return $this->trajet;
    }

    public function setTrajet(Trajet $trajet): static
    {
        $this->trajet = $trajet;

        return $this;
    }
}
