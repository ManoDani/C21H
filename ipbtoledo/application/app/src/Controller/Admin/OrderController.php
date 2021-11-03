<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller\Admin;

use Farol360\AncoraEad\Controller;
use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\EntityFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class OrderController extends Controller
{
    protected $entityFactory;
    protected $orderModel;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $order,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->orderModel = $order;
        $this->entityFactory = $entityFactory;
    }

    public function index(Request $request, Response $response): Response
    {
        $statusCodes = [
            1 => "Aguardando Pagamento",
            2 => "Em análise",
            3 => "Paga",
            4 => "Disponível",
            5 => "Em disputa",
            6 => "Devolvida",
            7 => "Cancelada",
        ];
        $orders = $this->orderModel->getOrders();
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $order->status = $statusCodes[$order->status];
            }
        }

        return $this->view->render($response, 'admin/order/index.twig', [
            'orders' => $orders
        ]);
    }
}
