<?php

namespace App\Entity;

use App\Repository\SignalementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SignalementRepository::class)]
class Signalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raison = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'signalements')]
    private Collection $signale;

    public function __construct()
    {
        $this->signale = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getSignale(): Collection
    {
        return $this->signale;
    }

    public function addSignale(User $signale): static
    {
        if (!$this->signale->contains($signale)) {
            $this->signale->add($signale);
        }

        return $this;
    }

    public function removeSignale(User $signale): static
    {
        $this->signale->removeElement($signale);

        return $this;
    }
}
