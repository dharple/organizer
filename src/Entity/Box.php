<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoxRepository")
 */
class Box implements DisplayableInterface
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="boxes")
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BoxModel", inversedBy="boxes")
     */
    private $boxModel;

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

    public function setDescription(?string $description): self
    {
        $this->description = $description ?? '';

        return $this;
    }

    public function getDisplayId()
    {
        return empty($this->getId()) ? '' : sprintf('%04d', $this->getId());
    }

    public function getDisplayLabel()
    {
        return empty($this->getId()) ? '' : sprintf('Box %04d - %s', $this->getId(), $this->getLabel());
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getBoxModel(): ?BoxModel
    {
        return $this->boxModel;
    }

    public function setBoxModel(?BoxModel $boxModel): self
    {
        $this->boxModel = $boxModel;

        return $this;
    }
}
