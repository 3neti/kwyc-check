<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\WalletController;
use App\Actions\ProcessHypervergeResult;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\RecruitedAgentController;
use App\Http\Controllers\CheckedinContactController;
use App\Http\Controllers\CheckinController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::resource('organizations', OrganizationController::class)
        ->only(['index', 'store', 'update']);
    Route::resource('checkins', CheckinController::class)
        ->only(['index', 'store']);
    Route::get('/wallet', WalletController::class)->name('wallet.index');
    Route::resource('campaigns', CampaignController::class)
        ->only(['index', 'store', 'update']);
});

Route::webhooks('webhook-paynamics-paybiz', 'paynamics-paybiz');
Route::get('hyperverge-api/result', ProcessHypervergeResult::class)->name('hyperverge-result');

Route::get('/register-agent/{code}', [AgentController::class,'recruit'])->name('register-agent');
Route::get('/recruit/{voucher}', [RecruitedAgentController::class, 'create'])->name('create-recruit');
Route::post('/recruit/{voucher}', [RecruitedAgentController::class, 'store'])->name('store-recruit');

