<?php


namespace Tries\Helpers;


class StringHelper
{
    public static function stringToArray(string $string) : array
    {
        return preg_split('//u', $string, null, PREG_SPLIT_NO_EMPTY);
    }
}