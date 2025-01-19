<?php

namespace App\Entity;

use App\Repository\FreelancerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "freelancer")]
class Freelancer extends User
{


    #[ORM\Column(length: 255)]
    private ?string $skills = null;

    #[ORM\Column(length: 255)]
    private ?string $portfolio = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\Column]
    private ?int $completedProjects = null;

    #[ORM\Column]
    private ?float $rating = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'freelancer')]
    private Collection $payments;

    public function __construct()
    {
        parent::__construct();
        $this->payments = new ArrayCollection();
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(string $skills): static
    {
        $this->skills = $skills;

        return $this;
    }

    public function getPortfolio(): ?string
    {
        return $this->portfolio;
    }

    public function setPortfolio(string $portfolio): static
    {
        $this->portfolio = $portfolio;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getCompletedProjects(): ?int
    {
        return $this->completedProjects;
    }

    public function setCompletedProjects(int $completedProjects): static
    {
        $this->completedProjects = $completedProjects;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setFreelancer($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getFreelancer() === $this) {
                $payment->setFreelancer(null);
            }
        }

        return $this;
    }
}
