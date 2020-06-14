<?php

use Larapress\Profiles\Repository\Filter;

interface IFilterRepository
{
    /**
     * @param string $type
     *
     * @return Filter[]
     */
    public function getByType($type);

    /**
     * @param string $type
     *
     * @return Filter
     */
    public function randomByType($type);

    /**
     * @param string $type
     *
     * @return Filter[]
     */
    public function getVisibleByType($type);

    /**
     * @param string $type
     *
     * @return Filter
     */
    public function randomVisibleByType($type);
}
