<?php
namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private ?int $id = null;

#[ORM\Column(length: 255)]
#[Assert\NotBlank(message: "Le titre est obligatoire.")]
#[Assert\Length(max: 255, maxMessage: "Le titre ne doit pas dépasser 255 caractères.")]
private ?string $title = null;

#[ORM\Column(type: Types::TEXT)]
#[Assert\NotBlank(message: "La description est obligatoire.")]
private ?string $description = null;

#[ORM\Column(type: Types::TEXT)]
#[Assert\NotBlank(message: "Les critères sont obligatoires.")]
private ?string $criteria = null;

#[ORM\Column]
#[Assert\NotBlank(message: "Le budget est obligatoire.")]
#[Assert\Positive(message: "Le budget doit être un nombre positif.")]
private ?float $budget = null;

#[ORM\Column(type: Types::DATETIME_MUTABLE)]
#[Assert\NotBlank(message: "La date limite est obligatoire.")]
#[Assert\GreaterThan("today", message: "La date limite doit être dans le futur.")]
private ?\DateTimeInterface $deadline = null;

#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
private ?User $client = null;

#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
private ?User $freelancer = null;

// Getters and Setters
public function getId(): ?int
{
return $this->id;
}

public function getTitle(): ?string
{
return $this->title;
}

public function setTitle(?string $title): self
{
$this->title = $title;

return $this;
}

public function getDescription(): ?string
{
return $this->description;
}

public function setDescription(?string $description): self
{
$this->description = $description;

return $this;
}

public function getCriteria(): ?string
{
return $this->criteria;
}

public function setCriteria(?string $criteria): self
{
$this->criteria = $criteria;

return $this;
}

public function getBudget(): ?float
{
return $this->budget;
}

public function setBudget(?float $budget): self
{
$this->budget = $budget;

return $this;
}

public function getDeadline(): ?\DateTimeInterface
{
return $this->deadline;
}

public function setDeadline(?\DateTimeInterface $deadline): self
{
$this->deadline = $deadline;

return $this;
}
    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getFreelancer(): ?User
    {
        return $this->freelancer;
    }

    public function setFreelancer(?User $freelancer): self
    {
        $this->freelancer = $freelancer;

        return $this;
    }


}
