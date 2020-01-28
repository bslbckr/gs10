<?php
declare(strict_types=1);

namespace guc\tournaments\controller;

class RegistrationController {

    private $regRepo;
    private $tournamentRepo;
    public function __construct(\guc\tournaments\repositories\RegistrationRepository $repo,
                                \guc\tournaments\repositories\TournamentRepository $tRepo) {
        $this->regRepo = $repo;
        $this->tournamentRepo = $tRepo;
    }

    public function handleRegistration(\Slim\Psr7\Request $request,
                                       \Slim\Psr7\Response $response, string $name)
        : \Slim\Psr7\Response {
        $tournament = $this->tournamentRepo->getTournamentByAbbrev($name);
        if ($tournament != null) {
            $now = new \DateTime();
            $regOpens = new \DateTime($tournament->reg_opens);
            $regCloses = new \DateTime($tournament->reg_closes);
            if ($now < $regOpens) {
                return $response->withStatus(400, "registration hasn't opened, yet");
            } else if ($now > $regCloses) {
                return $response->withStatus(400, "registration has already closed.");
            }

            $reg = $this->getRegistrationFromRequest($request);
            $reg->tournament_id = $tournament->id;

            $this->regRepo->storeRegistration($reg);
            $returnResponse = $response->withHeader('Content-type', 'application/json');
            $returnResponse->getBody()->write('{"success":true}');
            return $returnResponse;
        } else {
            return $response->withStatus(404, 'Tournament: '.$abbrev .' not found.');
        }
    }

    private function getRegistrationFromRequest(\Slim\Psr7\Request $req)
        : \guc\tournaments\model\Registration {
        $contentArr = $req->getParsedBody();
        $result = new \guc\tournaments\model\Registration();
        $result->team = $contentArr['team'];
        $result->city = $contentArr['city'];
        $result->contact_name = $contentArr['contactName'];
        $result->email = $contentArr['email'];
        $result->application_link = $contentArr['applicationLink'];
        $result->strength = $contentArr['strength'];
        return $result;
    }
}
