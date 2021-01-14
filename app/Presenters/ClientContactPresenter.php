<?php

namespace App\Presenters;

/**
 * Class ClientContactPresenter
 * @package App\Models\Presenters
 */
class ClientContactPresenter extends EntityPresenter
{

    /**
     * @return string
     */
    public function name()
    {
        if (!empty($this->entity->first_name) && !empty($this->entity->last_name)) {
            return $this->entity->first_name . ' ' . $this->entity->last_name;
        }

        return $this->entity->customer->name ?: '';
    }
}
