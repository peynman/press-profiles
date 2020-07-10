<?php

namespace Larapress\Profiles\Repository\Filter;

use Larapress\Profiles\Models\Filter;

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
    public function getVisibleByType($user, $type)
    {
        return $this->getByType($type);
    }

    /**
     * @param string $type
     *
     * @return Filter
     */
    public function randomVisibleByType($user, $type)
    {
        return $this->randomByType($type);
    }
}
