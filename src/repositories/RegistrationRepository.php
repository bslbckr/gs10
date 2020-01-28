<?php
declare(strict_types=1);

namespace guc\tournaments\repositories;

class RegistrationRepository {

    private $insertStmt;
    
    public function __construct(\PDO $pdo) {
        $this->insertStmt = $pdo->prepare('INSERT INTO registrations (tournament_id, team, city, contact_name, email, application_link, strength) VALUES (:tournament, :team, :city, :contact, :mail, :application, :strength)');
    }

    public function __destruct() {
        $this->insertStmt = null;
    }

    public function storeRegistration(\guc\tournaments\model\Registration $registration) : void {

        $this->insertStmt->execute(
            array('tournament' => $registration->tournament_id,
                  'team' => $registration->team,
                  'city' => $registration->city,
                  'contact' => $registration->contact_name,
                  'mail' => $registration->email,
                  'application' => $registration->application_link,
                  'strength' => $registration->strength));
    }
}
