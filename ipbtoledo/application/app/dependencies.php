<?php
declare(strict_types=1);

$container = $app->getContainer();

$container['cache'] = function ($c) {
    return new Slim\HttpCache\CacheProvider();
};

// Database adapter
$container['db'] = function ($c) {
    $db = $c->get('settings')['db'];

    $dsn = 'mysql:host=' . $db['host'];
    $dsn .= ';dbname=' . $db['name'];
    $dsn .= ';port=' . $db['port'];
    $dsn .= ';charset=' . $db['charset'];

    $pdo = new PDO($dsn, $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    return $pdo;
};

// Flash messages
$container['flash'] = function ($c) {
    return new Slim\Flash\Messages();
};

// Monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    if (!empty($settings['path'])) {
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    } else {
        $logger->pushHandler(new Monolog\Handler\ErrorLogHandler(0, Monolog\Logger::DEBUG, true, true));
    }
    return $logger;
};

// Mailer
$container['mailer'] = function ($c) {
    $settings = $c->get('settings')['mail'];
    date_default_timezone_set('UTC');

    $mailer = new PHPMailer();
    $mailer->setLanguage('pt_br');
    $mailer->isSMTP();
    $mailer->isHTML(false);
    $mailer->CharSet = 'UTF-8';
    $mailer->SMTPAuth = true;
    $mailer->SMTPDebug = 0;
    $mailer->Debugoutput = 'html';
    $mailer->Host = $settings['host'];
    $mailer->Port = $settings['port'];
    $mailer->SMTPSecure = $settings['smtpSecureType'];
    $mailer->Username = $settings['username'];
    $mailer->Password = $settings['password'];
    $mailer->setFrom($settings['username'], 'Website IPB Toledo');
    $mailer->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];
    if ($c->get('settings')['displayErrorDetails']) {
        $mailer->SMTPDebug = 3;
    }

    return new Farol360\AncoraEad\Mailer($mailer);
};

// Parsedown: Markdown parser
$container['markdown'] = function ($c) {
    return new Farol360\AncoraEad\MarkdownParser();
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->get('view')->render(
            $response->withStatus(404),
            '404.twig'
        );
    };
};

// Embera
$container['oembed'] = function ($c) {
    return new Embera\Embera([
        'allow' => ['Youtube', 'Vimeo','Facebook'],
        'params' => [
            'responsive' => true
        ],
    ]);
};

// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings')['view'];
    $view = new Slim\Views\Twig($settings['template_path'], $settings['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    $view->addExtension(new Farol360\AncoraEad\Twig\AuthExtension());
    $view->addExtension(new Farol360\AncoraEad\Twig\GravatarExtension());
    $view->addExtension(new Farol360\AncoraEad\Twig\MarkdownExtension());

    return $view;
};

Farol360\AncoraEad\User::setupUser($container);

// Controllers
$container['Farol360\AncoraEad\Controller\Admin\CourseController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\CourseController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\CourseModel($c['db']),
        new Farol360\AncoraEad\Model\ModuleModel($c['db']),
        new Farol360\AncoraEad\Model\LevelModel($c['db']),
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\IndexController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\IndexController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\BannerModel($c['db']),
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\BannerController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\BannerController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\BannerModel($c['db']),
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\ConfigController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\ConfigController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\PostSerieModel($c['db']),
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\SeriesController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\SeriesController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\PostModel($c['db']),
        new Farol360\AncoraEad\Model\PostSerieModel($c['db']),
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\LevelController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\LevelController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\LevelModel($c['db']),
        new Farol360\AncoraEad\Model\ModuleModel($c['db']),
        new Farol360\AncoraEad\Model\AttachmentModel($c['db']),
        $c['oembed'],
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\ModuleController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\ModuleController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\ModuleModel($c['db']),
        new Farol360\AncoraEad\Model\CourseModel($c['db']),
        new Farol360\AncoraEad\Model\LevelModel($c['db']),
        $c['markdown'],
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\OrderController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\OrderController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\OrderModel($c['db']),
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\PostController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\PostController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\PostModel($c['db']),
        new Farol360\AncoraEad\Model\PostTypeModel($c['db']),
        new Farol360\AncoraEad\Model\PostSerieModel($c['db']),
        new Farol360\AncoraEad\Model\PostFileModel($c['db']),
        new Farol360\AncoraEad\Model\PostTagModel($c['db']),
        $c['oembed'],
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\PostController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\PostController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\PostModel($c['db']),
        new Farol360\AncoraEad\Model\PostTypeModel($c['db']),
        new Farol360\AncoraEad\Model\PostFileModel($c['db']),
        new Farol360\AncoraEad\Model\PostSerieModel($c['db']),
        $c['oembed'],
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\PermissionController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\PermissionController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\PermissionModel($c['db']),
        new Farol360\AncoraEad\Model\RoleModel($c['db']),
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\RoleController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\RoleController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\RoleModel($c['db']),
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\Admin\UserController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\Admin\UserController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\UserModel($c['db']),
        new Farol360\AncoraEad\Model\RoleModel($c['db']),
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\CourseController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\CourseController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\CourseModel($c['db']),
        new Farol360\AncoraEad\Model\ModuleModel($c['db']),
        new Farol360\AncoraEad\Model\LevelModel($c['db']),
        new Farol360\AncoraEad\Model\UserCourseModel($c['db']),
        new Farol360\AncoraEad\Model\UserModel($c['db']),
        $c['oembed']
    );
};

$container['Farol360\AncoraEad\Controller\OrderController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\OrderController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\OrderModel($c['db']),
        new Farol360\AncoraEad\Model\CourseModel($c['db']),
        new Farol360\AncoraEad\Model\UserCourseModel($c['db']),
        new Farol360\AncoraEad\Model\UserModel($c['db']),
        $c->get('settings')['pagseguro'],
        new Farol360\AncoraEad\Model\EntityFactory()
    );
};

$container['Farol360\AncoraEad\Controller\PageController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\PageController(
        $c['view'],
        $c['flash'],
        $c['mailer'],
        new Farol360\AncoraEad\Model\BannerModel($c['db']),
        new Farol360\AncoraEad\Model\PostModel($c['db']),
        new Farol360\AncoraEad\Model\PostSerieModel($c['db']),
        $c['oembed']
    );
};

$container['Farol360\AncoraEad\Controller\UserController'] = function ($c) {
    return new Farol360\AncoraEad\Controller\UserController(
        $c['view'],
        $c['flash'],
        new Farol360\AncoraEad\Model\UserModel($c['db']),
        $c['mailer']
    );
};
