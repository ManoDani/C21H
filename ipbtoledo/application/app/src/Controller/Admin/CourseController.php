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

class CourseController extends Controller
{
    protected $courseModel;
    protected $entityFactory;
    protected $levelModel;
    protected $moduleModel;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $course,
        Model $module,
        Model $level,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->courseModel = $course;
        $this->moduleModel = $module;
        $this->levelModel = $level;
        $this->entityFactory = $entityFactory;
    }

    public function index(Request $request, Response $response): Response
    {
        $courses = $this->courseModel->getAll();
        return $this->view->render($response, 'admin/course/index.twig', [
            'courses' => $courses,
        ]);
    }

    public function add(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {
            return $this->view->render($response, 'admin/course/add.twig');
        }
        $course = $this->entityFactory->createCourse($request->getParsedBody());
        $course->id = (int)$this->courseModel->add($course);

        if ($course->id !== null) {
            $files = $request->getUploadedFiles();
            if (!empty($files['image'])) {
                $image = $files['image'];
                if ($image->getError() === UPLOAD_ERR_OK) {
                    $filename = sprintf(
                        '%s.%s',
                        uniqid(),
                        pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                    );
                    $path = 'upload/img/';
                    $image->moveTo($path . $filename);
                    $course->image = $path . $filename;
                    $this->courseModel->update($course);
                }
            }
            $this->flash->addMessage('success', "Curso adicionado com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/courses/' . $course->id);
        }
        $this->flash->addMessage('danger', "Erro ao adicionar curso.");
        return $this->httpRedirect($request, $response, '/admin/courses');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $courseId = intval($args['id']);
        $this->courseModel->delete($courseId);
        $this->flash->addMessage('success', "Curso removido com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/courses');
    }

    public function disable(Request $request, Response $response, array $args): Response
    {
        $courseId = intval($args['id']);
        $this->courseModel->disable($courseId);
        $this->flash->addMessage('success', "Curso desabilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/courses');
    }

    public function enable(Request $request, Response $response, array $args): Response
    {
        $courseId = intval($args['id']);
        $this->courseModel->enable($courseId);
        $this->flash->addMessage('success', "Curso habilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/courses');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $courseId = intval($args['id']);
        $course = $this->courseModel->get($courseId);

        if (!$course) {
            $this->flash->addMessage('danger', "Curso não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }

        return $this->view->render($response, 'admin/course/edit.twig', [
            'course' => $course
        ]);
    }

    public function removeImage(Request $request, Response $response, array $args): Response
    {
        $courseId = intval($args['id']);
        $course = $this->courseModel->get($courseId);

        if (!$course) {
            $this->flash->addMessage('danger', "Curso não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }

        if (!unlink($course->image)) {
            $this->flash->addMessage('danger', "Não foi possível remover a imagem.");
            return $this->httpRedirect($request, $response, '/admin/courses/edit/' . $course->id);
        }
        $course->image = null;
        $this->courseModel->update($course);

        $this->flash->addMessage('success', "Imagem removida com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/courses/edit/' . $course->id);
    }

    public function update(Request $request, Response $response): Response
    {
        $course = $this->entityFactory->createCourse($request->getParsedBody());

        $files = $request->getUploadedFiles();
        if (!empty($files['image']->file)) {
            $image = $files['image'];
            if ($image->getError() === UPLOAD_ERR_OK) {
                $filename = sprintf(
                    '%s.%s',
                    uniqid(),
                    pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                );
                $path = 'upload/img/';
                $image->moveTo($path . $filename);
                $course->image = $path . $filename;
            }
        } else {
            $oldCourse = $this->courseModel->get((int)$course->id);
            $course->image = $oldCourse->image;
        }
        $this->courseModel->update($course);

        $this->flash->addMessage('success', "Curso atualizado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/courses/' . $course->id);
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        $courseId = intval($args['id']);
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $this->flash->addMessage('danger', "Curso não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }

        $modules = $this->moduleModel->getModules((int)$course->id);
        if ($modules) {
            foreach ($modules as $module) {
                $module->levels = $this->levelModel->getLevels((int)$module->id);
            }
        }

        return $this->view->render($response, 'admin/course/view.twig', [
            'course' => $course,
            'modules' => $modules
        ]);
    }
}
