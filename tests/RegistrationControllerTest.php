<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use guc\tournaments\controller\RegistrationController;

final class RegistrationControllerTest extends TestCase {

    protected $controller;

    protected function setUp() : void {
        $tournRepo = $this->getMockBuilder(\guc\tournaments\repositories\TournamentRepository::class)
                          ->disableOriginalConstructor()
                          ->getMock();
        $regRepo = $this->getMockBuilder(\guc\tournaments\repositories\RegistrationRepository::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        
        $this->controller = new RegistrationController($regRepo, $tournRepo);
    }
    
    public function testValidRequest(): void {
        
    }
}
