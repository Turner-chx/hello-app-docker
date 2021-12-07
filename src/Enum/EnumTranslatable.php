<?php
/**
 * Created by PhpStorm.
 * User: devinfo
 * Date: 12/08/2016
 * Time: 14:21
 */

namespace App\Enum;


interface EnumTranslatable
{
    public function getTranslationKey();

    public static function getChoices();

    public static function get(string $key = '');
}