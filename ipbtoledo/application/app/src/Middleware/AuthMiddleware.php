<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Middleware;

use Farol360\AncoraEad\User;
use Farol360\AncoraEad\Model;
use Slim\Flash\Messages as FlashMessages;

class AuthMiddleware
{
    private $flash;
    private $userModel;

    public function __construct(FlashMessages $flash, Model $user)
    {
        $this->flash = $flash;
        $this->userModel = $user;
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
        $route = $request->getUri()->getPath();
        $permissions = User::getPermissionsValue();
        
        if (isset($request->getAttributes()['route'])) {
            $arguments = $request->getAttributes()['route']->getArguments();
        } else {
            $arguments = '';
        }
        

        //remove last '/' if present
        $route = substr(
            $route,
            0,
            (strlen($route) - 1)
        ) . str_replace(
            '/',
            '',
            substr(
                $route,
                (strlen($route) - 1),
                (strlen($route) - 0)
            )
        );

        //remove first '/', replace others '/' for '_'
        //and replace '_\d+' for '_:id'
        $route = "p_" .
        preg_replace(
            "/(_\d+)/im",
            "_:id",
            str_replace(
                '/',
                '',
                substr($route, 0, 1)
            ) . substr(
                str_replace('/', '_', $route),
                1,
                strlen($route)
            )
        );

        //replace token for :token
        $route = preg_replace("/(!!\w+)/im", ":token", $route);
        $route = preg_replace("/(%21%21\w+)/im", ":token", $route);


        if ($arguments != '') {
            foreach ($arguments as $key => $value) {
                $route = str_replace($value, ':'.$key, $route);
            }
        }
        


        if (isset($permissions[$route])) {
            $allowed = $permissions[$route];
        } else {
            $allowed = false;
        }

        if (!User::isAuth()) {
            if (!$allowed) {
                $_SESSION['return'] = $request->getUri()->getPath();
                $this->flash->addMessage('errorLogin', "É necessário estar autenticado!");
                return $response->withStatus(302)->withHeader(
                    'Location',
                    $request->getUri()->getBaseUrl() . '/users/signin'
                );
            }
        } else {
            if ((session_id() != $this->userModel->get()->session)) {
                User::logout();
                $this->flash->addMessage('errorLogin', "Sua sessão não é mais válida!");
                return $response->withStatus(302)->withHeader(
                    'Location',
                    $request->getUri()->getBaseUrl() . '/users/signin'
                );
            } else {
                if ($route != "p_users_signin") {
                    if (!$allowed) {
                        return $response->withStatus(403);
                    }
                } else {
                    return $response->withStatus(302)->withHeader(
                        'Location',
                        $request->getUri()->getBaseUrl()
                    );
                }
            }
        }

        $response = $next($request, $response);
        return $response;
    }
}
