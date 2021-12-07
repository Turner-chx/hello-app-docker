<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 12/06/19
 * Time: 10:13
 */

namespace App\Enum;


use MyCLabs\Enum\Enum;

class HistoryEventsEnum extends Enum implements EnumTranslatable
{
    private static $translationKeys = [
        self::EDIT => 'app.enum.history_event.edit',
        self::NEW => 'app.enum.history_event.new',
        self::REPLACE => 'app.enum.history_event.replace',
        self::EVOLUTION => 'app.enum.history_event.evolution'

    ];
    public const EDIT = 'Modification';
    public const NEW = 'Création';
    public const REPLACE = 'Remplacement';
    public const EVOLUTION = 'Évolution';

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
        return isset(self::$translationKeys[$key]) ? self::$translationKeys[$key] : self::$translationKeys[self::NEW];
    }
}