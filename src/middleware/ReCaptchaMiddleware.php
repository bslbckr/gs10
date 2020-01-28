<?php
declare(strict_types=1);

namespace guc\tournaments\middleware;

use \Slim\Psr7\Request;
use \Slim\Routing\Route;
use \Psr\Http\Message\ResponseInterface as Response;

class ReCaptchaMiddleware {

    private $recaptcha;
    
    public function __construct(\ReCaptcha\ReCaptcha $recaptcha) {
        $this->recaptcha = $recaptcha;
    }

    public function __invoke(Request $request, Route $handler): Response {
        $captchAnswer = $request->getParsedBody()['g-recaptcha-response'];
        if ($captchAnswer != null) {
            $result = $this->recaptcha->verify($captchAnswer);
            if ($result->isSuccess()) {
                return $handler->handle($request);
            }
        }
        return new \Slim\Psr7\Response(400);
    }
}
