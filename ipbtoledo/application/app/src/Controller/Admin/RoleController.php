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

class RoleController extends Controller
{
    protected $entityFactory;
    protected $roleModel;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $role,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->roleModel = $role;
        $this->entityFactory = $entityFactory;
    }

    public function index(Request $request, Response $response): Response
    {
        $roles = $this->roleModel->getAll();

        return $this->view->render($response, 'admin/role/index.twig', [
            'roles' => $roles
        ]);
    }

    public function add(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {
            return $this->view->render($response, 'admin/role/add.twig');
        }
        $role = $this->entityFactory->createRole($request->getParsedBody());
        $this->roleModel->add($role);

        $this->flash->addMessage('success', 'Cargo adicionado com sucesso.');
        return $this->httpRedirect($request, $response, '/admin/role');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $this->roleModel->delete($id);

        $this->flash->addMessage('success', 'Cargo removido com sucesso.');
        return $this->httpRedirect($request, $response, '/admin/role');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $role = $this->roleModel->get($id);
        if (!$role) {
            $this->flash->addMessage('danger', 'Cargo não encontrado.');
            return $this->httpRedirect($request, $response, '/admin/role');
        }
        return $this->view->render($response, 'admin/role/edit.twig', ['role' => $role]);
    }

    public function update(Request $request, Response $response): Response
    {
        $role = $this->entityFactory->createRole($request->getParsedBody());
        $this->roleModel->update($role);

        $this->flash->addMessage('success', 'Cargo atualizado com sucesso.');
        return $this->httpRedirect($request, $response, '/admin/role');
    }
}
