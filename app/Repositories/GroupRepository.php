<?php

namespace App\Repositories;

use App\Models\Group;
use App\Repositories\Base\BaseRepository;

class GroupRepository extends BaseRepository
{
    /**
     * GroupRepository constructor.
     * @param Group $group_setting
     */
    public function __construct(Group $group_setting)
    {
        parent::__construct($group_setting);
    }

    /**
     * Gets the class name.
     *
     * @return     string  The class name.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return Group
     */
    public function findGroupById(int $id): Group
    {
        return $this->findOneOrFail($id);
    }

    public function save($data, Group $group_setting): ?Group
    {
        $group_setting->fill($data);
        $group_setting->save();

        return $group_setting;
    }
}
