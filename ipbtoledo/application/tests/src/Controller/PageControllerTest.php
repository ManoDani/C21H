<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Tests\Controller;

use Farol360\AncoraEad\Controller\PageController;
use Farol360\AncoraEad\Mailer;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Flash\Messages;
use Slim\Views\Twig;

/**
 * @covers Farol360\AncoraEad\Controller\PageController
 */
final class PageControllerTest extends TestCase
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

        $mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new PageController($view, $flashMessages, $mailer);

        $environment = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
        ]);
        $request = Request::createFromEnvironment($environment);
        $response = new Response();
        $response->write('foo bar');

        $view->expects($this->once())
            ->method('render')
            ->with($response, 'page/index.twig')
            ->willReturn($response);

        $response = $controller->index($request, $response);
        $this->assertSame('foo bar', (string)$response->getBody());
    }
}
