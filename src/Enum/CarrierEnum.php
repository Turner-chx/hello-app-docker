<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 19/10/10
 * Time: 16:12
 */

namespace App\Enum;

use MyCLabs\Enum\Enum;

class CarrierEnum extends Enum implements EnumTranslatable
{
    private static $translationKeys = [
        /*self::CHRONOPOST => 'app.enum.carrier.chronopost',
        self::COLISPRIVE => 'app.enum.carrier.colis_prive',
        self::GLS => 'app.enum.carrier.gls',*/
        self::COLISSIMO => 'app.enum.carrier.colissimo',
        self::TNT => 'app.enum.carrier.tnt',
        self::CHRONOPOST => 'app.enum.carrier.chronopost',
    ];
    /*public const CHRONOPOST = 'CHAOAG';
    public const COLISPRIVE = 'CPNORM';
    public const GLS = ' ';*/
    public const COLISSIMO = '1MDS';
    public const CHRONOPOST = 'CHROAG';
    public const TNT = 'TNAN';

    public function getTranslationKey()
    {
        return self::$translationKeys[$this->value];
    }

    public static function getChoices()
    {
        return self::$translationKeys;
    }

    public static function get(string $key = '')
    {
        return isset(self::$translationKeys[$key]) ? self::$translationKeys[$key] : self::$translationKeys[self::COLISSIMO];
    }
}