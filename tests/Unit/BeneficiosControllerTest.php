<?php

namespace Tests\Unit;

use App\Http\Controllers\BeneficiosController;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request;

class BeneficiosControllerTest extends TestCase {
    public function testIndex() {
        $request = Request::create('/index', 'GET');
        $controller = new BeneficiosController();

        $response = $controller->index($request);
        $this->assertEquals(200, $response->getStatusCode());

        $response->assertJsonStructure([
            'code',
            'success',
            'data' => [
                '*' => [
                    'year',
                    'num',
                    'beneficios' => [
                        '*' => [
                            'id_programa',
                            'monto',
                            'fecha_recepcion',
                            'fecha',
                            'ano',
                            'view',
                            'ficha'
                        ]
                    ]
                ]
            ]
        ]);

        $this->assertTrue($response->json()['success']);
        $this->assertNotEmpty($response->json()['data']);

        $data = $response->json()['data'];
        $years = array_column($data, 'year');
        $this->assertEquals($years, array_reverse($years));
    }
}