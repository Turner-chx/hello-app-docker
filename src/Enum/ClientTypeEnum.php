<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 04/06/19
 * Time: 09:50
 */

namespace App\Enum;

use MyCLabs\Enum\Enum;

class ClientTypeEnum extends Enum implements EnumTranslatable
{
    private static $translationKeys = [
        self::DEALER => 'app.enum.client_type.dealer',
        self::CUSTOMER => 'app.enum.client_type.customer',
    ];
    public const DEALER = 'dealer';
    public const CUSTOMER = 'customer';

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
        return isset(self::$translationKeys[$key]) ? self::$translationKeys[$key] : self::$translationKeys[self::DEALER];
    }
}