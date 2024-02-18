<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user', 'vehicule'])]
    private ?int $id = null;

    #[ORM\Column(length: 9)]
    #[Assert\NotBlank(message: "L'immatriculation ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: '/^[A-Z]{2}[-][0-9]{3}[-][A-Z]{2}$/',
        message: "L'immatriculation doit être au format XX-000-XX (lettres majuscules et chiffres)."
    )]
    #[Groups(['vehicule'])]
    private ?string $immatriculation = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['vehicule'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicule'])]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[Groups(['vehicule'])]
    private ?ModeleVehicule $modele = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getModele(): ?ModeleVehicule
    {
        return $this->modele;
    }

    public function setModele(?ModeleVehicule $modele): static
    {
        $this->modele = $modele;

        return $this;
    }
}
