<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoxRepository")
 */
class Box
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $location_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $box_type_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocationId(): ?int
    {
        return $this->location_id;
    }

    public function setLocationId(int $location_id): self
    {
        $this->location_id = $location_id;

        return $this;
    }

    public function getBoxTypeId(): ?int
    {
        return $this->box_type_id;
    }

    public function setBoxTypeId(?int $box_type_id): self
    {
        $this->box_type_id = $box_type_id;

        return $this;
    }

    public function getDisplayId()
    {
        return empty($this->getId()) ? '' : sprintf("%04d", $this->getId());
    }

    public function getDisplayLabel()
    {
        return empty($this->getId()) ? '' : sprintf("Box %04d - %s", $this->getId(), $this->getLabel());
    }
}
