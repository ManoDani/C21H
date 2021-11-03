<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller;

use Embera\Embera as Oembed;
use Farol360\AncoraEad\Controller;
use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class CourseController extends Controller
{
    protected $courseModel;
    protected $levelModel;
    protected $moduleModel;
    protected $oembed;
    protected $userCourseModel;
    protected $userModel;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $course,
        Model $module,
        Model $level,
        Model $userCourse,
        Model $user,
        Oembed $oembed
    ) {
        parent::__construct($view, $flash);
        $this->courseModel = $course;
        $this->moduleModel = $module;
        $this->levelModel = $level;
        $this->userCourseModel = $userCourse;
        $this->userModel = $user;
        $this->oembed = $oembed;
    }

    public function index(Request $request, Response $response): Response
    {
        $courses = $this->courseModel->getAll();
        return $this->view->render($response, 'course/index.twig', [
            'courses' => $courses,
        ]);
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $course = $this->courseModel->get($id);
        if (!$course) {
            return $this->httpRedirect($request, $response, '/courses');
        }

        $modules = $this->moduleModel->getModules($id);
        if ($modules) {
            foreach ($modules as $module) {
                $module->levels = $this->levelModel->getLevels((int)$module->id);
            }
        }

        $hasCourse = false;
        if (User::isAuth()) {
            $user = $this->userModel->get();
            $userCourse = $this->userCourseModel->get((int)$user->id, (int)$course->id);
            if ($userCourse !== false || $course->price == 0) {
                $hasCourse = true;
            }
        }

        $isAuth = User::isAuth();

        return $this->view->render($response, 'course/view.twig', [
            'course' => $course,
            'modules' => $modules,
            'has_course' => $hasCourse,
            'is_auth' => $isAuth,
        ]);
    }

    public function viewLevel(Request $request, Response $response, array $args): Response
    {
        $courseId = intval($args['courseId']);
        $levelNumber = intval($args['levelNumber']);
        $moduleNumber = intval($args['moduleNumber']);
        $user = $this->userModel->get();
        $userCourse = $this->userCourseModel->get((int)$user->id, (int)$courseId);
        $course = $this->courseModel->get($courseId);

        if ($userCourse === false) {
            if ($course->price == 0) {
                //$this->userCourseModel->add($user->id, $courseId);
            } else {
                return $this->httpRedirect($request, $response, '/course/' . $courseId);
            }
        }

        $modules = $this->moduleModel->getModules((int)$course->id);
        foreach ($modules as $module) {
            if ($module->number == $moduleNumber) {
                $course->module = $module;
                break;
            }
        }
        $levels = $this->levelModel->getLevels((int)$course->module->id);
        foreach ($levels as $level) {
            if ($level->number == $levelNumber) {
                $course->module->level = $level;
                $course->module->level->embed = $this->oembed->autoEmbed(
                    $course->module->level->video
                );
                break;
            }
        }

        $lastModule = $this->moduleModel->getGreatestNumber((int)$course->id)['max_number'];
        $lastLevel = $this->levelModel->getGreatestNumber((int)$course->module->id)['max_number'];

        $nextDisabled = false;
        $previousDisabled = false;

        if ($course->module->level->number < $lastLevel) {
            $nextModule = $previousModule = $course->module->number;
            $nextLevel = $course->module->level->number + 1;
        } elseif ($course->module->number < $lastModule) {
            $nextModule = $course->module->number + 1;
            $nextLevel = 1;
        }
        if ($course->module->level->number > 1) {
            $previousLevel = $course->module->level->number - 1;
        } elseif ($course->module->level->number == 1) {
            $previousLevel = 1;
            if ($course->module->number == 1) {
                $previousDisabled = true;
            }
        }
        if ($course->module->number > 1) {
            $previousModule = $course->module->number;
            if ($course->module->level->number == 1) {
                $previousModule = $course->module->number - 1;
                // @todo Voltar ao maior nível do módulo anterior
                $previousLevel = 1;
            }
        }
        if ($course->module->number == $lastModule
            && $course->module->level->number == $lastLevel) {
            $nextDisabled = true;
        }

        return $this->view->render($response, 'course/level.twig', [
            'course' => $course,
            'next_module' => $nextModule,
            'next_level' => $nextLevel,
            'next_disabled' => $nextDisabled,
            'previous_module' => $previousModule,
            'previous_level' => $previousLevel,
            'previous_disabled' => $previousDisabled,
        ]);
    }
}
