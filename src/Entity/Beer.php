<?php

namespace App\Entity;

use App\Repository\BeerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BeerRepository::class)
 * @UniqueEntity("externalId")
 */
class Beer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"show_beer"})
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", unique = true)
     */
    private int $externalId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_beer"})
     */
    private string $name;

    /**
     * @ORM\Column(type="integer", length=255)
     */
    private int $capacityMilliliter;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     * @Groups({"show_beer"})
     */
    private float $price;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_beer"})
     */
    private string $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_beer"})
     */
    private string $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brewer", inversedBy="beers", cascade={"persist", "merge", "refresh"})
     * @Groups({"show_beer"})
     */
    private Brewer $brewer;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     * @Groups({"show_beer"})
     */
    private float $pricePerLitre;

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
     * @return int
     */
    public function getExternalId(): int
    {
        return $this->externalId;
    }

    /**
     * @param int $externalId
     */
    public function setExternalId(int $externalId): void
    {
        $this->externalId = $externalId;
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
     * @return int
     */
    public function getCapacityMilliliter(): int
    {
        return $this->capacityMilliliter;
    }

    /**
     * @param int $capacityMilliliter
     */
    public function setCapacityMilliliter(int $capacityMilliliter): void
    {
        $this->capacityMilliliter = $capacityMilliliter;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Brewer
     */
    public function getBrewer(): Brewer
    {
        return $this->brewer;
    }

    /**
     * @param Brewer $brewer
     */
    public function setBrewer(Brewer $brewer): void
    {
        $this->brewer = $brewer;
    }

    /**
     * @return float
     */
    public function getPricePerLitre(): float
    {
        return $this->pricePerLitre;
    }

    /**
     * @param float $pricePerLitre
     */
    public function setPricePerLitre(float $pricePerLitre): void
    {
        $this->pricePerLitre = $pricePerLitre;
    }
}