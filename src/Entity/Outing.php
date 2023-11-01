<?php

namespace App\Entity;

use App\Repository\OutingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutingRepository::class)]
class Outing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $outingDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $registrationDeadline = null;

    #[ORM\Column]
    private ?int $numberPlaces = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $outingImage = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\Column(length: 30)]
    private ?string $namePlace = null;

    #[ORM\Column(length: 150)]
    private ?string $street = null;

    #[ORM\Column]
    private ?int $postalCode = null;

    #[ORM\Column(length: 50)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cancelReason = null;

    #[ORM\ManyToOne(inversedBy: 'outingsCreated')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'outingsRegistrations')]
    private Collection $registrants;

    public function __construct()
    {
        $this->registrants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOutingDate(): ?\DateTimeInterface
    {
        return $this->outingDate;
    }

    public function setOutingDate(\DateTimeInterface $outingDate): static
    {
        $this->outingDate = $outingDate;

        return $this;
    }

    public function getRegistrationDeadline(): ?\DateTimeInterface
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(\DateTimeInterface $registrationDeadline): static
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

    public function getNumberPlaces(): ?int
    {
        return $this->numberPlaces;
    }

    public function setNumberPlaces(int $numberPlaces): static
    {
        $this->numberPlaces = $numberPlaces;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOutingImage(): ?string
    {
        return $this->outingImage;
    }

    public function setOutingImage(string $outingImage): static
    {
        $this->outingImage = $outingImage;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getNamePlace(): ?string
    {
        return $this->namePlace;
    }

    public function setNamePlace(string $namePlace): static
    {
        $this->namePlace = $namePlace;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCancelReason(): ?string
    {
        return $this->cancelReason;
    }

    public function setCancelReason(string $cancelReason): static
    {
        $this->cancelReason = $cancelReason;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getRegistrants(): Collection
    {
        return $this->registrants;
    }

    public function addRegistrant(User $registrant): static
    {
        if (!$this->registrants->contains($registrant)) {
            $this->registrants->add($registrant);
        }

        return $this;
    }

    public function removeRegistrant(User $registrant): static
    {
        $this->registrants->removeElement($registrant);

        return $this;
    }
}
