<?php

namespace Larapress\Profiles\Repository\Filter;

use Larapress\Profiles\Models\Filter;

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
    public function getVisibleByType($user, $type);

    /**
     * @param string $type
     *
     * @return Filter
     */
    public function randomVisibleByType($user, $type);
}
