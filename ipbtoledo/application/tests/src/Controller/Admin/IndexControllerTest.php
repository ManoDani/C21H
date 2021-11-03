<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Tests\Controller\Admin;

use Farol360\AncoraEad\Controller\Admin\IndexController;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Flash\Messages;
use Slim\Views\Twig;

/**
 * @covers Farol360\AncoraEad\Controller\Admin\IndexController
 */
final class IndexControllerTest extends TestCase
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

        $controller = new IndexController($view, $flashMessages);

        $environment = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/admin',
        ]);
        $request = Request::createFromEnvironment($environment);
        $response = new Response();
        $response->write('foo bar');

        $view->expects($this->once())
            ->method('render')
            ->with($response, 'admin/index/index.twig')
            ->willReturn($response);

        $response = $controller->index($request, $response);
        $this->assertSame('foo bar', (string)$response->getBody());
    }
}
