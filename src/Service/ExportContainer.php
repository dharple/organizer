<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\Box;
use App\Entity\BoxModel;
use App\Entity\Location;
use Exception;

/**
 * Exporter container
 */
class ExportContainer
{
    /**
     * @var Box[]
     */
    protected $boxes;

    /**
     * @var BoxModel[]
     */
    protected $boxModels;

    /**
     * @var Location[]
     */
    protected $locations;

    /**
     * Add box
     */
    public function addBox(Box $box)
    {
        $this->boxes[] = $box;
        return $this;
    }

    /**
     * Add box model
     */
    public function addBoxModel(BoxModel $boxModel)
    {
        $this->boxModels[] = $boxModel;
        return $this;
    }

    /**
     * Add location
     */
    public function addLocation(Location $location)
    {
        $this->locations[] = $location;
        return $this;
    }

    /**
     * Get box by id
     *
     * @return Box
     * @throws Exception
     */
    public function getBox(int $id)
    {
        foreach ($this->boxes as $box) {
            if ($box->getId() == $id) {
                return $box;
            }
        }

        throw new Exception('Could not find Box ID ' . $id);
    }

    /**
     * Get boxes
     *
     * @return Box[]
     */
    public function getBoxes()
    {
        return $this->boxes;
    }

    /**
     * Get box model by id
     *
     * @return BoxModel
     * @throws Exception
     */
    public function getBoxModel(int $id)
    {
        foreach ($this->boxModels as $boxModel) {
            if ($boxModel->getId() == $id) {
                return $boxModel;
            }
        }

        throw new Exception('Could not find Box Model ID ' . $id);
    }

    /**
     * Get box models
     *
     * @return BoxModel[]
     */
    public function getBoxModels()
    {
        return $this->boxModels;
    }

    /**
     * Get location by id
     *
     * @return Location
     * @throws Exception
     */
    public function getLocation(int $id)
    {
        foreach ($this->locations as $location) {
            if ($location->getId() == $id) {
                return $location;
            }
        }

        throw new Exception('Could not find Location ID ' . $id);
    }

    /**
     * Get locations
     *
     * @return Location[]
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Set boxes
     *
     * @param Box[] $boxes
     */
    public function setBoxes(array $boxes)
    {
        $this->boxes = $boxes;
        return $this;
    }

    /**
     * Set box models
     *
     * @param BoxModel[] $boxModels
     */
    public function setBoxModels(array $boxModels)
    {
        $this->boxModels = $boxModels;
        return $this;
    }

    /**
     * Set locations
     *
     * @param Location[] $locations
     */
    public function setLocations(array $locations)
    {
        $this->locations = $locations;
        return $this;
    }
}
