<?php

namespace App\Components;

class Arrays
{
    public static function keysDeleted($new, $old)
    {
        return array_diff($old, $new);
    }

    public static function keysUpdated($new, $old)
    {
        return array_diff(
            array_diff_assoc($new, $old),
            static::keysCreated($new, $old)
        );
    }

    public static function keysCreated($new, $old)
    {
        if (empty($old)) {
            return $new;
        }

        return array_diff($new, $old);
    }
}