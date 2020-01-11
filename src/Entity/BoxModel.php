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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoxModelRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class BoxModel extends AbstractEntity implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $label;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Box", mappedBy="boxModel")
     */
    protected $boxes;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $make;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $model;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $size;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $color;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    protected $latch;

    public function __construct()
    {
        $this->boxes = new ArrayCollection();
    }

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

    /**
     * @return Collection|Box[]
     */
    public function getBoxes(): Collection
    {
        return $this->boxes;
    }

    public function addBox(Box $box): self
    {
        if (!$this->boxes->contains($box)) {
            $this->boxes[] = $box;
            $box->setBoxModel($this);
        }

        return $this;
    }

    public function removeBox(Box $box): self
    {
        if ($this->boxes->contains($box)) {
            $this->boxes->removeElement($box);
            // set the owning side to null (unless already changed)
            if ($box->getBoxModel() === $this) {
                $box->setBoxModel(null);
            }
        }

        return $this;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function setMake(?string $make): self
    {
        $this->make = $make;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getLatch(): ?string
    {
        return $this->latch;
    }

    public function setLatch(?string $latch): self
    {
        $this->latch = $latch;

        return $this;
    }

    public function getDisplayLabel(): string
    {
        return $this->getLabel();
    }
}
