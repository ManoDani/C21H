<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Middleware;

use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig;

class FlashMessagesMiddleware
{
    private $flash;
    private $view;

    public function __construct(FlashMessages $flash, Twig $view)
    {
        $this->flash = $flash;
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
        $messages = $this->flash->getMessages();

        foreach ($messages as $key => $value) {
            $messages[$key] = $value[0];
        }

        $this->view->offsetSet('flash', $messages);

        $response = $next($request, $response);
        return $response;
    }
}
