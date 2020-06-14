<?php

use Larapress\Profiles\Repository\Filter;

class FilterRepository implements IFilterRepository
{
    /**
     * @param string $type
     *
     * @return Filter[]
     */
    public function getByType($type)
    {
        return Filter::where('type', $type)->get();
    }

    /**
     * @param string $type
     *
     * @return Filter
     */
    public function randomByType($type)
    {
        $filters = $this->getByType($type);
        return $filters[rand(0, count($filters))];
    }

    /**
     * @param string $type
     *
     * @return Filter[]
     */
    public function getVisibleByType($type)
    {

    }

    /**
     * @param string $type
     *
     * @return Filter
     */
    public function randomVisibleByType($type)
    {
        // TODO: Implement randomVisibleByType() method.
    }
}
