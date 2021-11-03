<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Twig;

use Twig_Extension;
use Twig_Function;

class GravatarExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            new Twig_Function(
                'gravatar_url',
                [$this, 'getGravatarUrl']
            )
        ];
    }

    public function getGravatarUrl($email, $size = 80, $default = 'mm', $rating = 'g')
    {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$size&d=$default&r=$rating";
        return $url;
    }
}
