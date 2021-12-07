<?php
/**
 * Created by PhpStorm.
 * User: maxencebeno
 * Date: 2019-03-15
 * Time: 16:35
 */

namespace App\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Twig_Extensions_Extension_Instance_Of extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('instanceOf', [$this, 'instanceOf']),
        ];
    }

    /**
     * Filter for converting dates to a time ago string like Facebook and Twitter has.
     *
     * @param $object
     * @param string $class
     * @return bool
     */
    public function instanceOf($object, string $class): bool
    {
        return $object instanceof $class;
    }
}