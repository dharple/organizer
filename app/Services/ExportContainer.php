<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services;

use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use Exception;

/**
 * Container for a full export of boxes, box models, and locations.
 */
class ExportContainer
{
    /**
     * The boxes in this export.
     *
     * @var Box[]
     */
    protected array $boxes = [];

    /**
     * The box models in this export.
     *
     * @var BoxModel[]
     */
    protected array $boxModels = [];

    /**
     * The locations in this export.
     *
     * @var Location[]
     */
    protected array $locations = [];

    /**
     * Adds a box to this container.
     */
    public function addBox(Box $box): self
    {
        $this->boxes[] = $box;
        return $this;
    }

    /**
     * Adds a box model to this container.
     */
    public function addBoxModel(BoxModel $boxModel): self
    {
        $this->boxModels[] = $boxModel;
        return $this;
    }

    /**
     * Adds a location to this container.
     */
    public function addLocation(Location $location): self
    {
        $this->locations[] = $location;
        return $this;
    }

    /**
     * Returns the box with the given ID.
     *
     * @throws Exception
     */
    public function getBox(int $id): Box
    {
        foreach ($this->boxes as $box) {
            if ($box->id == $id) {
                return $box;
            }
        }
        throw new Exception('Could not find Box ID ' . $id);
    }

    /**
     * Returns all boxes in this container.
     *
     * @return Box[]
     */
    public function getBoxes(): array
    {
        return $this->boxes;
    }

    /**
     * Returns the box model with the given ID.
     *
     * @throws Exception
     */
    public function getBoxModel(int $id): BoxModel
    {
        foreach ($this->boxModels as $boxModel) {
            if ($boxModel->id == $id) {
                return $boxModel;
            }
        }
        throw new Exception('Could not find Box Model ID ' . $id);
    }

    /**
     * Returns all box models in this container.
     *
     * @return BoxModel[]
     */
    public function getBoxModels(): array
    {
        return $this->boxModels;
    }

    /**
     * Returns the location with the given ID.
     *
     * @throws Exception
     */
    public function getLocation(int $id): Location
    {
        foreach ($this->locations as $location) {
            if ($location->id == $id) {
                return $location;
            }
        }
        throw new Exception('Could not find Location ID ' . $id);
    }

    /**
     * Returns all locations in this container.
     *
     * @return Location[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * Sets the boxes for this container.
     *
     * @param Box[] $boxes
     */
    public function setBoxes(array $boxes): self
    {
        $this->boxes = $boxes;
        return $this;
    }

    /**
     * Sets the box models for this container.
     *
     * @param BoxModel[] $boxModels
     */
    public function setBoxModels(array $boxModels): self
    {
        $this->boxModels = $boxModels;
        return $this;
    }

    /**
     * Sets the locations for this container.
     *
     * @param Location[] $locations
     */
    public function setLocations(array $locations): self
    {
        $this->locations = $locations;
        return $this;
    }
}
