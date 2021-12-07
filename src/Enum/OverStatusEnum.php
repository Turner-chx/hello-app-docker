<?php
/**
 * Created by PhpStorm.
 * User: maxencebeno
 * Date: 2019-01-15
 * Time: 16:26
 */

namespace App\Enum;


use MyCLabs\Enum\Enum;

class OverStatusEnum extends Enum implements EnumTranslatable
{
    private static $translationKeys = [
        self::OVER => 'app.enum.over_status.over',
        self::OPEN => 'app.enum.over_status.open',
    ];
    public const OVER = 'over';
    public const OPEN = 'open';

    public function getTranslationKey()
    {
        return self::$translationKeys[$this->value];
    }

    public static function getChoices(): array
    {
        return self::$translationKeys;
    }

    public static function get(string $key = '')
    {
        return self::$translationKeys[$key] ?? self::$translationKeys[self::OVER];
    }
}