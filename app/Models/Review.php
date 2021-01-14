<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public function contact()
    {
        return $this->belongsTo(CustomerContact::class);
    }

}