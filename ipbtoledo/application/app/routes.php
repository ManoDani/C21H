<?php
declare(strict_types=1);

use Farol360\AncoraEad\Controller\Admin\CourseController as AdminCourse;
use Farol360\AncoraEad\Controller\Admin\BannerController as BannerAdmin;
use Farol360\AncoraEad\Controller\Admin\PostController as PostCourse;
use Farol360\AncoraEad\Controller\Admin\ConfigController as ConfigController;
use Farol360\AncoraEad\Controller\Admin\SeriesController as SeriesController;
use Farol360\AncoraEad\Controller\Admin\IndexController as AdminIndex;
use Farol360\AncoraEad\Controller\Admin\LevelController as AdminLevel;
use Farol360\AncoraEad\Controller\Admin\ModuleController as AdminModule;
use Farol360\AncoraEad\Controller\Admin\OrderController as AdminOrder;
use Farol360\AncoraEad\Controller\Admin\PermissionController as AdminPermission;
use Farol360\AncoraEad\Controller\Admin\RoleController as AdminRole;
use Farol360\AncoraEad\Controller\Admin\UserController as AdminUser;
use Farol360\AncoraEad\Controller\CourseController as Course;
use Farol360\AncoraEad\Controller\OrderController as Order;
use Farol360\AncoraEad\Controller\PostController as Post;
use Farol360\AncoraEad\Controller\PageController as Page;
use Farol360\AncoraEad\Controller\UserController as User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Rotas Default
$app->get('[/]', Page::class . ':index');
$app->get('/index', Page::class . ':index');
$app->map(['GET', 'POST'], '/contato', Page::class . ':contato');
$app->get('/obrigado', Page::class . ':contatoObrigado');
$app->get('/manutencao', Page::class . ':manutencao');

//Rotas IPB
$app->get('/midia', Post::class . ':index');
$app->get('/midia/fotos', Post::class . ':midia_fotos');
$app->get('/midia/videos', Post::class . ':midia_videos');

$app->get('/igreja', Page::class . ':igreja');
$app->get('/igreja/historia', Page::class . ':igrejaHistoria');
$app->get('/igreja/lideranca', Page::class . ':igrejaLiderancas');
$app->get('/igreja/horarios', Page::class . ':manutencao');

$app->get('/onde_estamos', Page::class . ':onde_estamos');

$app->get('/cuidado_pastoral', Page::class . ':cuidado_pastoral');
$app->map(['GET', 'POST'] , '/cuidado_pastoral/pedido', Page::class . ':cuidado_pastoral_pedido');
$app->map(['GET', 'POST'] , '/cuidado_pastoral/aconselhamento', Page::class . ':cuidado_pastoral_aconselhamento');
$app->map(['GET', 'POST'] , '/cuidado_pastoral/outros', Page::class . ':cuidado_pastoral_outros');

$app->map(['GET', 'POST'], '/oracao', Page::class . ':oracao');

$app->get('/postagens', Post::class . ':index');

$app->get('/posts/{id:[0-9]+}[/]', Post::class . ':postid');
$app->get('/series/{slug}[/]', Post::class . ':serieId');

$app->get('/mensagens_series', Page::class . ':mensagens_series');

$app->get('/mensagem_atual', Page::class . ':mensagem_atual');
$app->get('/serie_especial', Page::class . ':serie_especial');

$app->get('/serie_atual', Page::class . ':serie_atual');



//Rotas âncora
/*
$app->get('/courses', Course::class . ':index');

$app->group('/order', function () {
    $this->get('/{id:[0-9]+}', Order::class . ':cart');
    $this->map(['GET', 'POST'], '/pagseguro', Order::class . ':pagSeguro');
});

$app->group('/course', function () {
    $this->get('/{id:[0-9]+}', Course::class . ':view');
    $this->get(
        '/{courseId:[0-9]+}/module/{moduleNumber:[0-9]+}/level/{levelNumber:[0-9]+}',
        Course::class . ':viewLevel'
    );
});
*/
$app->group('/users', function () {
    $this->get('/dashboard', User::class . ':dashboard');
    $this->map(['GET', 'POST'], '/profile', User::class . ':profile');
    $this->map(['GET', 'POST'], '/recover', User::class . ':recover');
    $this->map(['GET', 'POST'], '/recover/token/{token}', User::class . ':recoverPassword');
    $this->map(['GET', 'POST'], '/signin', User::class . ':signIn');
    $this->get('/signout', User::class . ':signOut');
    $this->map(['GET', 'POST'], '/signup', User::class . ':signUp');
    $this->get('/verify/{token}', User::class . ':verify');
});

$app->group('/teste', function () {
    $this->get('/testePost', Post::class . ':testePost');

});

$app->group('/admin', function () {
    $this->get('[/]', AdminIndex::class . ':index');

    $this->group('/banners', function() {
        $this->map(['GET', 'POST'], '/add', BannerAdmin::class . ':add');
        $this->map(['GET', 'POST'], '/delete/{id:[0-9]+}', BannerAdmin::class . ':delete');
        $this->map(['GET', 'POST'], '/edit[/{id:[0-9]+}]', BannerAdmin::class . ':edit');
    });

    $this->get('/orders', AdminOrder::class . ':index');

    $this->group('/courses', function () {
        $this->get('[/]', AdminCourse::class . ':index');
        $this->map(['GET', 'POST'], '/add', AdminCourse::class . ':add');
        $this->get('/{id:[0-9]+}', AdminCourse::class . ':view');
        $this->get('/{id:[0-9]+}/disable', AdminCourse::class . ':disable');
        $this->get('/{id:[0-9]+}/enable', AdminCourse::class . ':enable');
        $this->get('/delete/{id:[0-9]+}', AdminCourse::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', AdminCourse::class . ':edit');
        $this->get('/remove-image/{id:[0-9]+}', AdminCourse::class . ':removeImage');
        $this->post('/update', AdminCourse::class . ':update');
    });

    $this->group('/configs', function() {
        $this->map(['GET', 'POST'], '[/]', ConfigController::class . ':config');
    });

    $this->group('/series', function() {
        $this->map(['GET', 'POST'], '[/]', SeriesController::class . ':series');
        $this->map(['GET', 'POST'], '/add', SeriesController::class . ':add');
        $this->map(['GET', 'POST'],'/delete/{id:[0-9]+}', SeriesController::class . ':delete');
        $this->map(['GET', 'POST'], '/edit[/{id:[0-9]+}]', SeriesController::class . ':edit');
        $this->get('/{id:[0-9]+}/disable', SeriesController::class . ':disable');
        $this->get('/{id:[0-9]+}/enable', SeriesController::class . ':enable');
        $this->post('/alteraSerieAtual', SeriesController::class . ':alteraSerieAtual');
        $this->post('/alteraProgramaDevocional', SeriesController::class . ':alteraProgramaDevocional');
        $this->post('/alteraSerieEspecial', SeriesController::class . ':alteraSerieEspecial');
    });
    $this->group('/posts', function () {
        $this->get('[/]', PostCourse::class . ':index');
        $this->map(['GET', 'POST'], '/add', PostCourse::class . ':add');
        $this->get('/{id:[0-9]+}', PostCourse::class . ':edit');
        $this->get('/{id:[0-9]+}/disable', PostCourse::class . ':disable');
        $this->get('/{id:[0-9]+}/enable', PostCourse::class . ':enable');
        $this->get('/delete/{id:[0-9]+}', PostCourse::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', PostCourse::class . ':edit');
        $this->get('/galeria/{id:[0-9]+}', PostCourse::class . ':galeria');
        $this->post('/galeria/add', PostCourse::class . ':addimagem');
        $this->get('/galeria/remove/{id:[0-9]+}', PostCourse::class . ':removeImagemGaleria');
        $this->get('/remove-image/{id:[0-9]+}', PostCourse::class . ':removeImage');
        $this->post('/update', PostCourse::class . ':update');
        $this->post('/{id:[0-9]+}/tag/add', PostCourse::class . ':addTag');
        $this->post('/{id:[0-9]+}/tag/remove', PostCourse::class . ':removeTag');
    });

    $this->group('/levels', function () {
        $this->map(['GET', 'POST'], '/add', AdminLevel::class . ':add');
        $this->get('/{id:[0-9]+}', AdminLevel::class . ':view');
        $this->get('/delete/{id:[0-9]+}', AdminLevel::class . ':delete');
        $this->get(
            '/edit/{id:[0-9]+}[/{action}[/{attachment:[0-9]+}]]',
            AdminLevel::class . ':edit'
        );
        $this->post('/update', AdminLevel::class . ':update');
    });

    $this->group('/modules', function () {
        $this->map(['GET', 'POST'], '/add', AdminModule::class . ':add');
        $this->get('/{id:[0-9]+}', AdminModule::class . ':view');
        $this->get('/delete/{id:[0-9]+}', AdminModule::class . ':delete');
        $this->get('/edit/{id:[0-9]+}[/{action}]', AdminModule::class . ':edit');
        $this->post('/update', AdminModule::class . ':update');
    });

    $this->group('/permission', function () {
        $this->get('[/]', AdminPermission::class . ':index');
        $this->map(['GET', 'POST'], '/add', AdminPermission::class . ':add');
        $this->get('/delete/{id:[0-9]+}', AdminPermission::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', AdminPermission::class . ':edit');
        $this->post('/update', AdminPermission::class . ':update');
    });

    $this->group('/role', function () {
        $this->get('[/]', AdminRole::class . ':index');
        $this->map(['GET', 'POST'], '/add', AdminRole::class . ':add');
        $this->get('/delete/{id:[0-9]+}', AdminRole::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', AdminRole::class . ':edit');
        $this->post('/update', AdminRole::class . ':update');
    });

    $this->group('/user', function () {
        $this->get('[/]', AdminUser::class . ':index');
        $this->get('/all', AdminUser::class . ':index');
        $this->get('/export', AdminUser::class . ':export');
        $this->get('/{id:[0-9]+}', AdminUser::class . ':view');
        $this->map(['GET', 'POST'], '/add', AdminUser::class . ':add');
        $this->get('/delete/{id:[0-9]+}', AdminUser::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', AdminUser::class . ':edit');
        $this->post('/update', AdminUser::class . ':update');
    });
});

$app->get('/hash', Page::class . ':hash');
