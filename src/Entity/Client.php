<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "client")]
class Client extends User
{
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $ratings = null;


    public function getRatings(): ?float
    {
        return $this->ratings;
    }

    public function setRatings(float $ratings): static
    {
        $this->ratings = $ratings;

        return $this;
    }


}
