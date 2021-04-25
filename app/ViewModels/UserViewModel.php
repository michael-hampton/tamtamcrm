<?php


namespace App\ViewModels;


use App\Models\User;

class UserViewModel extends ViewModel
{

    /**
     * @var User 
     */
    private User $user;

    /**
     * UserViewModel constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function name()
    {
        $first_name = isset($this->user->first_name) ? $this->user->first_name : '';
        $last_name = isset($this->user->last_name) ? $this->user->last_name : '';

        return $first_name . ' ' . $last_name;
    }
}