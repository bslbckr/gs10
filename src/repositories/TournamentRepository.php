<?php
declare(strict_types=1);

namespace guc\tournaments\repositories;
use \PDO;
class TournamentRepository {

    private $getStmt;
    
    public function __construct(\PDO $pdo) {
        $this->getStmt = $pdo->prepare('SELECT id, abbrev, reg_opens, reg_closes, fullname, spots FROM tournaments WHERE abbrev = :abbrev');
        $this->getStmt->setFetchMode(PDO::FETCH_CLASS, 'guc\tournaments\model\Tournament');
    }

    public function getTournamentByAbbrev(string $abbrev) : \guc\tournaments\model\Tournament {
        $this->getStmt->bindParam(':abbrev', $abbrev);
        if ($this->getStmt->execute()) {
            $result = $this->getStmt->fetch();
        }
        return $result;
    }

    function __destruct() {
        $this->getStmt = null;
    }
}
