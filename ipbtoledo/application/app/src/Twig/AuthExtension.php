<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Twig;

use Twig_Extension;
use Twig_Function;

class AuthExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            new Twig_Function(
                'is_auth',
                'Farol360\AncoraEad\User::isAuth'
            ),
            new Twig_Function(
                'get_email',
                'Farol360\AncoraEad\User::getEmail'
            ),
        ];
    }
}
