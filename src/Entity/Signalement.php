<?php

namespace App\Entity;

use App\Repository\SignalementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SignalementRepository::class)]
class Signalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user', 'signalement'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La raison du signalement ne peut pas être vide.")]
    #[Groups(['signalement'])]
    private ?string $raison = null;

    #[ORM\ManyToOne(inversedBy: 'signalementsFaits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['signalement'])]
    private ?User $auteur = null;

    #[ORM\ManyToOne(inversedBy: 'signalementsPris')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['signalement'])]
    private ?User $destinataire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(string $raison): static
    {
        $this->raison = $raison;

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getDestinataire(): ?User
    {
        return $this->destinataire;
    }

    public function setDestinataire(?User $destinataire): static
    {
        $this->destinataire = $destinataire;

        return $this;
    }
}