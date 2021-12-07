<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 12/02/20
 * Time: 09:50
 */

namespace App\Enum;

use MyCLabs\Enum\Enum;

class SavTypeEnum extends Enum implements EnumTranslatable
{
    private static $translationKeys = [
        self::REPAIR => 'app.enum.sav_type.repair',
        self::VOUCHER => 'app.enum.sav_type.voucher',
        self::RESALE => 'app.enum.sav_type.resale',
    ];
    public const REPAIR = 'repair';
    public const VOUCHER = 'voucher';
    public const RESALE = 'resale';

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
        return self::$translationKeys[$key] ?? self::$translationKeys[self::REPAIR];
    }
}