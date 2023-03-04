<?php

use Illuminate\Support\Facades\Route;
use App\Actions\RegisterOrganization;
use App\Actions\RegisterUser;
use Illuminate\Http\Request;
use App\Actions\TopupUser;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register-agent/{org}', RegisterUser::class)->name('recruit-agent');

Route::group(['middleware' => ['auth:sanctum']], function ($route) {
    $route->post('/register-organization', RegisterOrganization::class);
    $route->post('/topup-user', TopupUser::class);
});
