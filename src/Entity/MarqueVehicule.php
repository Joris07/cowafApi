<?php

namespace App\Entity;

use App\Repository\MarqueVehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MarqueVehiculeRepository::class)]
class MarqueVehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['vehicule'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['vehicule'])]
    private ?string $marque = null;

    #[ORM\OneToMany(mappedBy: 'marque', targetEntity: ModeleVehicule::class)]
    private Collection $modeleVehicules;

    public function __construct()
    {
        $this->modeleVehicules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * @return Collection<int, ModeleVehicule>
     */
    public function getModeleVehicules(): Collection
    {
        return $this->modeleVehicules;
    }

    public function addModeleVehicule(ModeleVehicule $modeleVehicule): static
    {
        if (!$this->modeleVehicules->contains($modeleVehicule)) {
            $this->modeleVehicules->add($modeleVehicule);
            $modeleVehicule->setMarque($this);
        }

        return $this;
    }

    public function removeModeleVehicule(ModeleVehicule $modeleVehicule): static
    {
        if ($this->modeleVehicules->removeElement($modeleVehicule)) {
            // set the owning side to null (unless already changed)
            if ($modeleVehicule->getMarque() === $this) {
                $modeleVehicule->setMarque(null);
            }
        }

        return $this;
    }
}
