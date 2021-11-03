<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller\Admin;

use Farol360\AncoraEad\Controller;
use Farol360\AncoraEad\MarkdownParser;
use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\EntityFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class ModuleController extends Controller
{
    protected $courseModel;
    protected $entityFactory;
    protected $levelModel;
    protected $moduleModel;
    protected $markdown;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $module,
        Model $course,
        Model $level,
        MarkdownParser $markdown,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->courseModel = $course;
        $this->moduleModel = $module;
        $this->levelModel = $level;
        $this->markdown = $markdown;
        $this->entityFactory = $entityFactory;
    }

    public function add(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {
            if (!empty($request->getQueryParams())) {
                $courseId = (int) $request->getQueryParams()['course'];
            } else {
                $courseId = 0;
            }
            $courses = $this->courseModel->getAll();
            return $this->view->render($response, 'admin/module/add.twig', [
                'courses' => $courses,
                'course_id' => $courseId,
            ]);
        }
        $module = $this->entityFactory->createModule($request->getParsedBody());
        $module->number = intval($this->moduleModel->getGreatestNumber((int)$module->course_id)['max_number']);
        $module->number++;

        $this->moduleModel->add($module);

        $this->flash->addMessage('success', "Módulo adicionado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/courses/' . $module->course_id);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $module = $this->moduleModel->get($id);
        if (!empty($module)) {
            $this->moduleModel->delete((int)$module->id, (int)$module->course_id, (int)$module->number);
            $this->flash->addMessage('success', "Módulo excluído com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/courses/' . $module->course_id);
        } else {
            $this->flash->addMessage('danger', "Módulo não existente.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $module = $this->moduleModel->get($id);

        if (!$module) {
            $this->flash->addMessage('danger', "Módulo não existente.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }

        if (!empty($args['action'])) {
            if ($args['action'] == 'up' && $module->number > 1) {
                $this->moduleModel->swapUp((int)$module->id);
            } elseif ($args['action'] == 'down' &&
                $module->number < $this->moduleModel->getGreatestNumber((int)$module->course_id)['max_number']
            ) {
                $this->moduleModel->swapDown((int)$module->id);
            } else {
                return $this->httpRedirect($request, $response, '/admin/courses/' . $module->course_id);
            }
            return $this->httpRedirect($request, $response, '/admin/courses/' . $module->course_id);
        }

        $courses = $this->courseModel->getAll();

        return $this->view->render($response, 'admin/module/edit.twig', [
            'module' => $module,
            'courses' => $courses,
        ]);
    }

    public function update(Request $request, Response $response): Response
    {
        $module = $this->entityFactory->createModule($request->getParsedBody());
        $this->moduleModel->update($module);
        $this->flash->addMessage('success', "Módulo editado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/modules/' . $module->id);
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $module = $this->moduleModel->get($id);
        if (!$module) {
            $this->flash->addMessage('danger', "Módulo não existente.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }

        $levels = $this->levelModel->getLevels($id);
        if ($levels) {
            foreach ($levels as $level) {
                $level->content = $this->markdown->text($level->content);
            }
        }

        return $this->view->render($response, 'admin/module/view.twig', [
            'module' => $module,
            'levels' => $levels
        ]);
    }
}
