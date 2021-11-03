<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller\Admin;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Farol360\AncoraEad\Model\EntityFactory;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class ConfigController extends Controller
{

    protected $postSerieModel;
    protected $entityFactory;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $postSerieModel,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->postSerieModel = $postSerieModel;
        $this->entityFactory = $entityFactory;

    }

    public function config(Request $request, Response $response)
    {

        $postSerie = $this->postSerieModel->getAll();

        return $this->view->render($response, 'admin/config/config.twig', [
            'postSerie' => $postSerie]);
    }
}
