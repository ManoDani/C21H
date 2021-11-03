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

class IndexController extends Controller
{

    protected $bannerModel;
    protected $entityFactory;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $bannerModel,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->bannerModel = $bannerModel;
        $this->entityFactory = $entityFactory;

    }

    public function index(Request $request, Response $response): Response
    {
        //total to seria atual use
        $banners = $this->bannerModel->getAll();
        return $this->view->render($response, 'admin/index/index.twig', [
            'banners' => $banners]);
    }
}
