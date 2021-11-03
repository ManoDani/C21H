<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Middleware;

use Farol360\AncoraEad\User;
use Slim\Views\Twig;

class PermissionMiddleware
{
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $permissions = User::getPermissionsValue();

        //remove ':' as twig's variable doesn't accept it
        foreach ($permissions as $key => $value) {
            $this->view->offsetSet(str_replace(':', '', $key), $value);
        }

        $response = $next($request, $response);
        return $response;
    }
}
