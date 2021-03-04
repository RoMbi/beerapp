<?php

namespace App\Dto;

class BrewerDto
{
    private int $id;

    private string $name;

    private int $beersAssigned;

    public function __construct(int $id, string $name, int $beersAssigned)
    {
        $this->id = $id;
        $this->name = $name;
        $this->beersAssigned = $beersAssigned;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param int $beersAssigned
     */
    public function setBeersAssigned(int $beersAssigned): void
    {
        $this->beersAssigned = $beersAssigned;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getBeersAssigned(): int
    {
        return $this->beersAssigned;
    }
}
