<?php
declare(strict_types=1);

$app->add(new Slim\HttpCache\Cache('public', 86400));

// Monolog middleware
$app->add(function ($request, $response, $next) {
    $return = $next($request, $response);
    $context = [
        'status' => $return->getStatusCode(),
        'method' => $request->getMethod(),
        'route' => $request->getUri()->getPath(),
        'params' => $request->getQueryParams(),
    ];
    $this->logger->info($return->getStatusCode() . ' ' . $return->getReasonPhrase(), $context);
    return $return;
});

$app->add(new Farol360\AncoraEad\Middleware\PermissionMiddleware($app->getContainer()->view));

$app->add(new Farol360\AncoraEad\Middleware\AuthMiddleware(
    $app->getContainer()->flash,
    new Farol360\AncoraEad\Model\UserModel($app->getContainer()->db)
));

$app->add(new Farol360\AncoraEad\Middleware\FlashMessagesMiddleware(
    $app->getContainer()->flash,
    $app->getContainer()->view
));
