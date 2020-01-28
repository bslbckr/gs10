<?php
declare(strict_types=1);

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use guc\tournaments\repositories\TournamentRepository;

namespace guc\tournaments\controller;

class TournamentController {

    private $tournamentRepo;

    public function __construct(\guc\tournaments\repositories\TournamentRepository $repo) {
        $this->$tournamentRepo = $repo;
    }

    public function getTournament(\Slim\Psr7\Request $request, \Slim\Psr7\Response $response) : \Slim\Psr7\Response {
        $tournament = $this->$tournamentRepo->getTournamentByAbbrev('gs12');
        $response->getBody()->write(json_encode($tournament));
        $result = $response->withHeader('Content-type', 'application/json');
        return $result;
    }

    function __invoke(\Slim\Psr7\Request $request, \Slim\Psr7\Response $response) {
        //        if ($request->getRequestTarget() == '/') {
        return $this->getTournament($request, $response);
        //} else if
    }
    
}
