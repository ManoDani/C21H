<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Tests\Controller\Admin;

use Farol360\AncoraEad\Controller\Admin\CourseController;
use Farol360\AncoraEad\Model\CourseModel;
use Farol360\AncoraEad\Model\ModuleModel;
use Farol360\AncoraEad\Model\LevelModel;
use Farol360\AncoraEad\Model\EntityFactory;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Flash\Messages;
use Slim\Views\Twig;

/**
 * @covers \Farol360\AncoraEad\Controller\Admin\CourseController
 */
final class CourseControllerTest extends TestCase
{
    public function testIndex()
    {
        $view = $this->getMockBuilder(Twig::class)
            ->setMethods(['render'])
            ->disableOriginalConstructor()
            ->getMock();

        $flashMessages = $this->getMockBuilder(Messages::class)
            ->disableOriginalConstructor()
            ->getMock();

        $courseModel = $this->getMockBuilder(CourseModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $moduleModel = $this->getMockBuilder(ModuleModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $levelModel = $this->getMockBuilder(LevelModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityFactory = new EntityFactory();

        $controller = new CourseController(
            $view,
            $flashMessages,
            $courseModel,
            $moduleModel,
            $levelModel,
            $entityFactory
        );

        $environment = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/admin/courses',
        ]);
        $request = Request::createFromEnvironment($environment);
        $response = new Response();
        $response->write('Index page');

        $view->expects($this->once())
            ->method('render')
            ->with($response, 'admin/course/index.twig', ['courses' => []])
            ->willReturn($response);

        $response = $controller->index($request, $response);
        $this->assertSame('Index page', (string)$response->getBody());
    }
}
