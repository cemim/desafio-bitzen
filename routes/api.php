<?php

use App\Http\Controllers\ControllerPartnerCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('/v1/partners', ControllerPartnerCompany::class);
Route::put('/v1/restore-inative-cnpj/{partner}', [ControllerPartnerCompany::class, 'restore']);
Route::get('/v1/search-razao-social/{razaoSocial}', [ControllerPartnerCompany::class, 'searchRazaoSocial']);