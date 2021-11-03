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

class PermissionController extends Controller
{
    protected $entityFactory;
    protected $permissionModel;
    protected $roleModel;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $permission,
        Model $role,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->permissionModel = $permission;
        $this->roleModel = $role;
        $this->entityFactory = $entityFactory;
    }

    public function index(Request $request, Response $response): Response
    {
        $roles = $this->roleModel->getAll();
        $permissions = $this->permissionModel->getAll();

        return $this->view->render($response, 'admin/permission/index.twig', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    public function add(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {
            $roles = $this->roleModel->getAll();
            return $this->view->render($response, 'admin/permission/add.twig', [
                'roles' => $roles,
            ]);
        }
        $permission = $this->entityFactory->createPermission($request->getParsedBody());
        $this->permissionModel->add($permission);

        $this->flash->addMessage('success', 'Permissão adicionada com sucesso.');
        return $this->httpRedirect($request, $response, '/admin/permission');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $this->permissionModel->delete($id);
        $this->flash->addMessage('success', 'Permissão removida com sucesso.');
        return $this->httpRedirect($request, $response, '/admin/permission');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $permission = $this->permissionModel->get($id);

        if (!$permission) {
            $this->flash->addMessage('danger', 'Permissão não encontrada.');
            return $this->httpRedirect($request, $response, '/admin/permission');
        }

        $roles = $this->roleModel->getAll();

        return $this->view->render($response, 'admin/permission/edit.twig', [
            'roles' => $roles,
            'permission' => $permission
        ]);
    }

    public function update(Request $request, Response $response): Response
    {
        $permission = $this->entityFactory->createPermission($request->getParsedBody());
        $this->permissionModel->update($permission);

        $this->flash->addMessage('success', 'Permissão atualizada com sucesso.');
        return $this->httpRedirect($request, $response, '/admin/permission');
    }
}
