<?php

namespace App\Entity;

use App\Repository\BrewerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BrewerRepository::class)
 * @UniqueEntity("name")
 */
class Brewer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     * @Groups({"show_beer"})
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Beer", mappedBy="brewer", cascade={"all"})
     */
    private object $beers;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Beer[]
     */
    public function getBeers(): object
    {
        return $this->beers;
    }

    /**
     * @param object $beers
     */
    public function setBeers(object $beers): void
    {
        $this->beers = $beers;
    }

}