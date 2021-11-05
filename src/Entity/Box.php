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

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoxRepository")
 * @ORM\Table(name="box",uniqueConstraints={@ORM\UniqueConstraint(name="box_number_uniq", columns={"box_number"})})
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Box extends AbstractEntity implements EntityInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BoxModel", inversedBy="boxes")
     */
    protected $boxModel;

    /**
     * @ORM\Column(type="integer")
     */
    protected $boxNumber;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="boxes")
     */
    protected $location;

    /**
     * @ORM\PrePersist
     */
    public function generateBoxNumber(LifecycleEventArgs $event): void
    {
        if (isset($this->boxNumber)) {
            return;
        }

        $this->boxNumber = $event->getEntityManager()->getRepository(Box::class)->getNextBoxNumber();
    }

    public function getBoxModel(): ?BoxModel
    {
        return $this->boxModel;
    }

    public function getBoxNumber(): ?int
    {
        return $this->boxNumber;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDisplayId(): string
    {
        return empty($this->getBoxNumber()) ? '' : sprintf('%04d', $this->getBoxNumber());
    }

    public function getDisplayLabel(): string
    {
        return empty($this->getBoxNumber()) ? $this->getLabel() : sprintf('Box %04d - %s', $this->getBoxNumber(), $this->getLabel());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function isHidden(): bool
    {
        $location = $this->getLocation();
        return ($location === null) ? false : $location->getHidden();
    }

    public function setBoxModel(?BoxModel $boxModel): self
    {
        $this->boxModel = $boxModel;

        return $this;
    }

    public function setBoxNumber(int $boxNumber): self
    {
        $this->boxNumber = $boxNumber;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description ?? '';

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }
}
