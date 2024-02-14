<?php

namespace App\Entity;

use App\Repository\TrajetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetRepository::class)]
class Trajet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trajet', 'animal'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['trajet'])]
    private ?string $lieuDepart = null;

    #[ORM\Column(length: 255)]
    #[Groups(['trajet'])]
    private ?string $lieuDestination = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['trajet'])]
    private ?\DateTimeInterface $dateHeureDepart = null;

    #[ORM\Column]
    #[Groups(['trajet'])]
    private ?int $placesDisponible = null;

    #[ORM\Column]
    #[Groups(['trajet'])]
    private ?int $prixParPersonne = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['trajet'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'trajetsCrees', cascade : ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['trajet'])]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Animal::class, inversedBy: 'trajets')]
    #[Groups(['trajet'])]
    private Collection $animaux;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateSuppression = null;

    public function __construct()
    {
        $this->animaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLieuDepart(): ?string
    {
        return $this->lieuDepart;
    }

    public function setLieuDepart(string $lieuDepart): static
    {
        $this->lieuDepart = $lieuDepart;

        return $this;
    }

    public function getLieuDestination(): ?string
    {
        return $this->lieuDestination;
    }

    public function setLieuDestination(string $lieuDestination): static
    {
        $this->lieuDestination = $lieuDestination;

        return $this;
    }

    public function getDateHeureDepart(): ?\DateTimeInterface
    {
        return $this->dateHeureDepart;
    }

    public function setDateHeureDepart(\DateTimeInterface $dateHeureDepart): static
    {
        $this->dateHeureDepart = $dateHeureDepart;

        return $this;
    }

    public function getPlacesDisponible(): ?int
    {
        return $this->placesDisponible;
    }

    public function setPlacesDisponible(int $placesDisponible): static
    {
        $this->placesDisponible = $placesDisponible;

        return $this;
    }

    public function getPrixParPersonne(): ?int
    {
        return $this->prixParPersonne;
    }

    public function setPrixParPersonne(int $prixParPersonne): static
    {
        $this->prixParPersonne = $prixParPersonne;

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

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getanimaux(): Collection
    {
        return $this->animaux;
    }

    public function addAnimauxQuiVoyage(Animal $animauxQuiVoyage): static
    {
        if (!$this->animaux->contains($animauxQuiVoyage)) {
            $this->animaux->add($animauxQuiVoyage);
        }

        return $this;
    }

    public function removeAnimauxQuiVoyage(Animal $animauxQuiVoyage): static
    {
        $this->animaux->removeElement($animauxQuiVoyage);

        return $this;
    }

    public function getDateSuppression(): ?\DateTimeInterface
    {
        return $this->dateSuppression;
    }

    public function setDateSuppression(?\DateTimeInterface $dateSuppression): static
    {
        $this->dateSuppression = $dateSuppression;

        return $this;
    }
}
