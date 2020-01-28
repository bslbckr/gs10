<?php
declare(strict_types=1);

use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

require __DIR__ . '/../../vendor/autoload.php';

$settings = require __DIR__ . '/settings.php';

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(
    [
        'database.host' => $settings["hostname"],
        'database.db' => $settings["database"],
        'database.user' => $settings["user"],
        'database.password' => $settings["password"],
        'recaptchasecret' => $settings["reCaptchaSecret"],
        PDO::class => function(PSR\Container\ContainerInterface $c) {
            return new PDO('mysql:host='.$c->get('database.host').';dbname='.$c->get('database.db'),
                           $c->get('database.user'),
                           $c->get('database.password'));

        },
        \ReCaptcha\ReCaptcha::class =>function(PSR\Container\ContainerInterface $c) {
            return new \ReCaptcha\ReCaptcha($c->get('recaptchasecret'));
        }
    ]);
$container = $containerBuilder->build();

$app = \DI\Bridge\Slim\Bridge::create($container);
$callableResolver = $app->getCallableResolver();

$app->group('/api/{name}', function( Slim\Routing\RouteCollectorProxy $group) {
    $group->post('/registration', [\guc\tournaments\controller\RegistrationController::class, 'handleRegistration'])->add(\guc\tournaments\middleware\ReCaptchaMiddleware::class);
});
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$responseFactory = $app->getResponseFactory();
// $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

$request = ServerRequestCreatorFactory::create()->createServerRequestFromGlobals();

//$shutdownHandler = new ShutdownHandler($request,
//                                       $errorHandler, true);
//register_shutdown_function($shutdownHandler);

$errorMiddleWare = $app->addErrorMiddleware(true, false, false);
//$errorMiddleWare->setDefaultErrorHandler($errorHandler);

$response = $app->handle($request);
$responseEmitter = new Slim\ResponseEmitter();
$responseEmitter->emit($response);

$tournamentRepo = null;
$pdo = null;
?>
