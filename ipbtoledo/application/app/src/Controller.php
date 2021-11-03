<?php
declare(strict_types=1);

namespace Farol360\AncoraEad;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig;

abstract class Controller
{
    protected $flash;
    protected $view;

    public function __construct(Twig $view, FlashMessages $flash)
    {
        $this->flash = $flash;
        $this->view = $view;
    }

    protected function httpRedirect(
        Request $request,
        Response $response,
        string $path = '',
        int $status = 302
    ): Response {
        return $response->withStatus($status)->withHeader(
            'Location',
            $request->getUri()->getBaseUrl() . $path
        );
    }
}
