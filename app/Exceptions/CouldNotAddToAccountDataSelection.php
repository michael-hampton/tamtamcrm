<?php


namespace App\Exceptions;


class CouldNotAddToAccountDataSelection extends \Exception
{
    public static function fileAlreadyAddedToAccountDataSelection(string $path): self
    {
        return new static("Could not add `{$path}` because it already exists.");
    }
}