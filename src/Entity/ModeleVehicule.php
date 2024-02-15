<?php

namespace App\Entity;

use App\Repository\ModeleVehiculeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ModeleVehiculeRepository::class)]
class ModeleVehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['vehicule'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['vehicule'])]
    private ?string $modele = null;

    #[ORM\ManyToOne(inversedBy: 'modeleVehicules')]
    #[Groups(['vehicule'])]
    private ?MarqueVehicule $marque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getMarque(): ?MarqueVehicule
    {
        return $this->marque;
    }

    public function setMarque(?MarqueVehicule $marque): static
    {
        $this->marque = $marque;

        return $this;
    }
}
