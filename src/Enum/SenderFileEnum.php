<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 09/07/19
 * Time: 10:56
 */

namespace App\Enum;

use MyCLabs\Enum\Enum;

class SenderFileEnum extends Enum implements EnumTranslatable
{
    private static $translationKeys = [
        self::DEALER => 'app.enum.sender_file.dealer',
        self::CUSTOMER => 'app.enum.sender_file.customer',
        self::LAMA => 'app.enum.sender_file.lama',
    ];
    public const DEALER = 'dealer';
    public const CUSTOMER = 'customer';
    public const LAMA = 'lama';

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
        return isset(self::$translationKeys[$key]) ? self::$translationKeys[$key] : self::$translationKeys[self::LAMA];
    }
}