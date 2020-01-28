<?php
declare(strict_types=1);

namespace guc\tournaments\model;

class Registration {
    public $id;
    public $tournament_id;
    public $team;
    public $city;
    public $contact_name;
    public $email;
    public $registration_date;
    public $application_link;
    public $strength;
    public $confirmed = false;
    public $waiting_list = false;
    public $position;
    public $paid = false;
}
