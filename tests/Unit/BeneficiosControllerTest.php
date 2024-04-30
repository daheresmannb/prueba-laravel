<?php

namespace Tests\Unit;
 
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\BeneficiosController;
use Illuminate\Http\Request;

class BeneficiosControllerTest extends TestCase {

    public function testIndex() {
        $controller = new BeneficiosController();
        $request = new Request();

        $response = $controller->index($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}