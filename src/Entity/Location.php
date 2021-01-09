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
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Location extends AbstractEntity implements EntityInterface
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Box", mappedBy="location")
     */
    protected $boxes;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     */
    protected $parentLocation;

    public function __construct()
    {
        $this->boxes = new ArrayCollection();
    }

    public function addBox(Box $box): self
    {
        if (!$this->boxes->contains($box)) {
            $this->boxes[] = $box;
            $box->setLocation($this);
        }

        return $this;
    }

    /**
     * @return Collection|Box[]
     */
    public function getBoxes(): Collection
    {
        return $this->boxes;
    }

    /**
     * Generates the display label for this class, showing its full place on
     * the tree.
     *
     * @return string A full display label for this location.  For instance
     *                "Home - Garage - Wire Rack".
     *
     * @throws Exception
     */
    public function getDisplayLabel()
    {
        $build = [];
        foreach ($this->parentWalker() as $location) {
            $build[] = $location->getLabel();
        }

        return implode(' - ', array_reverse($build));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getParentLocation(): ?self
    {
        return $this->parentLocation;
    }

    /**
     * Generator that walks back through parents and yields each generation in
     * turn.
     *
     * Throws an exception if the same ID appears twice.
     *
     * @return Location[]
     *
     * @throws Exception
     */
    protected function parentWalker(): iterable
    {
        $ids = [];
        $objs = [];

        $location = $this;
        while ($location !== null) {
            if ($location->getId() !== null) {
                if (in_array($location->getId(), $ids)) {
                    throw new Exception('Recursive location hierarchy found');
                }
                $ids[] = $location->getId();
            } else {
                if (in_array($location, $objs)) {
                    throw new Exception('Recursive location hierarchy found');
                }
                $objs[] = $location;
            }

            yield $location;
            $location = $location->getParentLocation();
        }
    }

    public function removeBox(Box $box): self
    {
        if ($this->boxes->contains($box)) {
            $this->boxes->removeElement($box);
            // set the owning side to null (unless already changed)
            if ($box->getLocation() === $this) {
                $box->setLocation(null);
            }
        }

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setParentLocation(?self $parentLocation): self
    {
        $hold = $this->parentLocation;

        $this->parentLocation = $parentLocation;

        try {
            // check for recursion and bail if we find it
            iterator_to_array($this->parentWalker());
        } catch (Exception $e) {
            $this->parentLocation = $hold;
            throw $e;
        }

        return $this;
    }
}
