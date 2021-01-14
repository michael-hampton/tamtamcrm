<?php


namespace App\Factory;


use App\Models\Account;
use App\Models\Brand;
use App\Models\User;

class BrandFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @return Brand
     */
    public static function create(Account $account, User $user): Brand
    {
        $brand = new Brand;
        $brand->account_id = $account->id;
        $brand->user_id = $user->id;
        $brand->status = 1;

        return $brand;
    }
}