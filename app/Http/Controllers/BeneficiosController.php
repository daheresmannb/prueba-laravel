<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BeneficiosController extends Controller {
    public function index(Request $request) {
        try {
            $beneficios_response = Http::get(env("BENEFICIOS_URL"));
            $filtros_response = Http::get(env("FILTROS_URL"));
            $fichas_response = Http::get(env("FICHAS_URL"));

            $beneficios = collect($beneficios_response->json()["data"]);
            $filtros = collect($filtros_response->json()["data"]);
            $fichas = collect($fichas_response->json()["data"]);

            $beneficios_procesados = $beneficios->filter(
                function ($beneficio) use ($filtros) {
                    $filtros_rang = $filtros->firstWhere(
                        "id_programa", 
                        $beneficio["id_programa"]
                    );
                    return $beneficio["monto"] >= $filtros_rang["min"] && $beneficio["monto"] <= $filtros_rang["max"];
                }
            )->groupBy(
                function ($beneficio) {
                    return Carbon::parse($beneficio["fecha"])->year;
                }
            )->map(
                function ($beneficio_ano, $year) use ($fichas) {
                    return [
                        "year" => $year,
                        "num" => $beneficio_ano->count(),
                        "beneficios" => $beneficio_ano->map(
                            function ($beneficio) use ($fichas, $year) {
                                $beneficio["ano"] = strval($year);
                                $beneficio["view"] = true;
                                $beneficio["ficha"] = $fichas->where(
                                    "id_programa", 
                                    $beneficio["id_programa"]
                                )->first();
                                return $beneficio;
                            }
                        )
                    ];
                }
            )->sortByDesc("year")->values();

            return response()->json([
                "code" => 200,
                "success" => true,
                "data" => $beneficios_procesados,
            ]);
        } catch (Exception $e) {
            return response()->json([
                "code" => $e->getCode(),
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }
}