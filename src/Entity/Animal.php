<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
#[Vich\Uploadable]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trajet', 'user', 'animal'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le prenom ne peut pas être vide.")]
    #[Assert\Length(max: 50, maxMessage: "Le prenom ne peut pas dépasser {{ limit }} caractères")]
    #[Groups(['animal'])]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Assert\Range(
        max: 50,
        maxMessage: "L'âge de votre animal ne peut pas dépasser {{ limit }} ans"
    )]
    #[Assert\Type(
        type: 'integer',
        message: 'Entier uniquement accepté',
    )]
    #[Groups(['animal'])]
    private ?int $age = 1;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['animal'])]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Trajet::class, mappedBy: 'animaux')]
    #[Groups(['animal'])]
    private Collection $trajets;

    #[Vich\UploadableField(mapping: 'animalPhoto', fileNameProperty: 'imageName')]
    #[Groups(['animal'])]
    private ?File $photoAnimal = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['animal'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\ManyToMany(targetEntity: DescriptionAnimal::class, inversedBy: 'animals')]
    private Collection $description;

    public function __construct()
    {
        $this->trajets = new ArrayCollection();
        $this->description = new ArrayCollection();
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getPhotoAnimal(): ?File
    {
        return $this->photoAnimal;
    }

    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setPhotoAnimal(?File $imageFile = null): void
    {
        $this->photoAnimal = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

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

    /**
     * @return Collection<int, Trajet>
     */
    public function getTrajets(): Collection
    {
        return $this->trajets;
    }

    public function addTrajet(Trajet $trajet): static
    {
        if (!$this->trajets->contains($trajet)) {
            $this->trajets->add($trajet);
            $trajet->addAnimauxQuiVoyage($this);
        }

        return $this;
    }

    public function removeTrajet(Trajet $trajet): static
    {
        if ($this->trajets->removeElement($trajet)) {
            $trajet->removeAnimauxQuiVoyage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DescriptionAnimal>
     */
    public function getDescription(): Collection
    {
        return $this->description;
    }

    public function addDescription(DescriptionAnimal $description): static
    {
        if (!$this->description->contains($description)) {
            $this->description->add($description);
        }

        return $this;
    }

    public function removeDescription(DescriptionAnimal $description): static
    {
        $this->description->removeElement($description);

        return $this;
    }

}
