<?php

namespace App\Presenters;

/**
 * Class UserPresenter
 * @package App\Models\Presenters
 */
class UserPresenter extends EntityPresenter
{
    /**
     * @return string
     */
    public function name()
    {
        $first_name = isset($this->entity->first_name) ? $this->entity->first_name : '';
        $last_name = isset($this->entity->last_name) ? $this->entity->last_name : '';

        return $first_name . ' ' . $last_name;
    }
}
