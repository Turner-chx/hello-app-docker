<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 2020-10-12
 * Time: 14:01
 */

namespace App\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Twig_Extensions_Extension_Is_Image extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isImage', [$this, 'isImage']),
        ];
    }

    /**
     * Filter for converting dates to a time ago string like Facebook and Twitter has.
     *
     * @param $path
     * @return bool
     */
    public function isImage($path): bool
    {
        $image = @getimagesize($path);
        if (!$image) {
            return false;
        }
        $imageType = $image[2];
        $types = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG , IMAGETYPE_BMP];

        if (in_array($imageType, $types, true)) {
            return true;
        }
        return false;
    }
}