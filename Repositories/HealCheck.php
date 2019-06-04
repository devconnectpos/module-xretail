<?php

namespace SM\XRetail\Repositories;

use SM\XRetail\Repositories\Contract\ServiceAbstract;

/**
 * Class HealCheck
 *
 * @package SM\XRetail\Repositories
 */
class HealCheck extends ServiceAbstract
{
    public function getStatus()
    {
        return '1';
    }
}
