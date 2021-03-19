<?php

namespace App\Service\AdminFlock;

class Math
{
    static function clamp($value, $min, $max)
    {
        return max($min, min($max, $value));
    }
}