<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeneficiosController;

Route::get('/beneficios', [BeneficiosController::class, 'index']);