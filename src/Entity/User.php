<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trajet', 'user', 'animal', 'avis', 'vehicule', 'signalement', 'facture'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: "L'adresse email '{{ value }}' n'est pas valide")]
    #[Groups(['user'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user'])]
    private array $roles = ["ROLE_USER"];
    
    #[ORM\Column]
    #[Assert\PasswordStrength(
        message: "Le mot de passe doit contenir au moins 12 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial",
        minScore : PasswordStrength::STRENGTH_WEAK
    )]
    #[Groups(['user'])]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Length(max: 50, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères")]
    #[Groups(['user'])]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le prénom ne peut pas être vide.")]
    #[Assert\Length(max: 50, maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères")]
    #[Groups(['user'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50, maxMessage: "Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères")]
    #[Assert\Regex(
        pattern: '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/',
        message: 'Le numéro de téléphone doit être au format français (Exemple : +33789096525)'
    )]
    #[Groups(['user'])]
    private ?string $telephone = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La note ne peut pas être vide")]
    #[Assert\Range(
        min: 0,
        max: 5,
        notInRangeMessage: "La note doit être comprise entre {{ min }} et {{ max }}"
    )]
    #[Groups(['user'])]
    private ?int $note = 0;

    #[ORM\Column]
    #[Groups(['user'])]
    private ?bool $isAssociation = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Animal::class)]
    #[Groups(['user'])]
    private Collection $animals;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Trajet::class)]
    #[Groups(['user'])]
    private Collection $trajetsCrees;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Vehicule::class)]
    #[Groups(['user'])]
    private Collection $vehicules;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Facture::class)]
    #[Groups(['user'])]
    private Collection $factures;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Avis::class)]
    #[Groups(['user'])]
    private Collection $avisPostes;

    #[ORM\OneToMany(mappedBy: 'destinataire', targetEntity: Avis::class)]
    #[Groups(['user'])]
    private Collection $avisDestines;

    #[Vich\UploadableField(mapping: 'userPhoto', fileNameProperty: 'imageName')]
    #[Groups(['user'])]
    private ?File $photoProfil = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Signalement::class)]
    #[Groups(['user'])]
    private Collection $signalementsFaits;

    #[ORM\OneToMany(mappedBy: 'destinataire', targetEntity: Signalement::class)]
    #[Groups(['user'])]
    private Collection $signalementsPris;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
        $this->trajetsCrees = new ArrayCollection();
        $this->vehicules = new ArrayCollection();
        $this->factures = new ArrayCollection();
        $this->avisPostes = new ArrayCollection();
        $this->avisDestines = new ArrayCollection();
        $this->signalementsFaits = new ArrayCollection();
        $this->signalementsPris = new ArrayCollection();
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

    public function getPhotoProfil(): ?File
    {
        return $this->photoProfil;
    }

    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setPhotoProfil(?File $imageFile = null): void
    {
        $this->photoProfil = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function isIsAssociation(): ?bool
    {
        return $this->isAssociation;
    }

    public function setIsAssociation(bool $isAssociation): static
    {
        $this->isAssociation = $isAssociation;

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->setUser($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        if ($this->animals->removeElement($animal)) {
            // set the owning side to null (unless already changed)
            if ($animal->getUser() === $this) {
                $animal->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getTrajetsCrees(): Collection
    {
        return $this->trajetsCrees;
    }

    public function addTrajetsCree(Trajet $trajetsCree): static
    {
        if (!$this->trajetsCrees->contains($trajetsCree)) {
            $this->trajetsCrees->add($trajetsCree);
            $trajetsCree->setUser($this);
        }

        return $this;
    }

    public function removeTrajetsCree(Trajet $trajetsCree): static
    {
        if ($this->trajetsCrees->removeElement($trajetsCree)) {
            if ($trajetsCree->getUser() === $this) { }
                //$trajetsCree->setUser(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Vehicule>
     */
    public function getVehicules(): Collection
    {
        return $this->vehicules;
    }

    public function addVehicule(Vehicule $vehicule): static
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules->add($vehicule);
            $vehicule->setUser($this);
        }

        return $this;
    }

    public function removeVehicule(Vehicule $vehicule): static
    {
        if ($this->vehicules->removeElement($vehicule)) {
            // set the owning side to null (unless already changed)
            if ($vehicule->getUser() === $this) {
                $vehicule->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setUser($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getUser() === $this) {
                $facture->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisPostes(): Collection
    {
        return $this->avisPostes;
    }

    public function addAvisPoste(Avis $avis): static
    {
        if (!$this->avisPostes->contains($avis)) {
            $this->avisPostes->add($avis);
            $avis->getAuteur($this);
        }

        return $this;
    }

    public function removeAvisPoste(Avis $avis): static
    {
        if ($this->avisPostes->removeElement($avis)) {
            if ($avis->getAuteur() === $this) {
                $avis->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisDestines(): Collection
    {
        return $this->avisDestines;
    }

    public function addAvisDestine(Avis $avis): static
    {
        if (!$this->avisDestines->contains($avis)) {
            $this->avisDestines->add($avis);
            $avis->getDestinataire($this);
        }

        return $this;
    }

    public function removeAvisDestine(Avis $avis): static
    {
        if ($this->avisDestines->removeElement($avis)) {
            // set the owning side to null (unless already changed)
            if ($avis->getDestinataire() === $this) {
                $avis->setDestinataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Signalement>
     */
    public function getSignalementsFaits(): Collection
    {
        return $this->signalementsFaits;
    }

    public function addSignalementsFait(Signalement $signalementsFait): static
    {
        if (!$this->signalementsFaits->contains($signalementsFait)) {
            $this->signalementsFaits->add($signalementsFait);
            $signalementsFait->setAuteur($this);
        }

        return $this;
    }

    public function removeSignalementsFait(Signalement $signalementsFait): static
    {
        if ($this->signalementsFaits->removeElement($signalementsFait)) {
            // set the owning side to null (unless already changed)
            if ($signalementsFait->getAuteur() === $this) {
                $signalementsFait->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Signalement>
     */
    public function getSignalementsPris(): Collection
    {
        return $this->signalementsPris;
    }

    public function addSignalementsPri(Signalement $signalementsPri): static
    {
        if (!$this->signalementsPris->contains($signalementsPri)) {
            $this->signalementsPris->add($signalementsPri);
            $signalementsPri->setDestinataire($this);
        }

        return $this;
    }

    public function removeSignalementsPri(Signalement $signalementsPri): static
    {
        if ($this->signalementsPris->removeElement($signalementsPri)) {
            // set the owning side to null (unless already changed)
            if ($signalementsPri->getDestinataire() === $this) {
                $signalementsPri->setDestinataire(null);
            }
        }

        return $this;
    }
}
