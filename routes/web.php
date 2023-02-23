<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\WalletController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    Route::get('/register-user/{org_id}', function ($org_id) {
        return Inertia::render('RegisterUser', compact('org_id'));
    })->name('register-user');
    Route::resource('organizations', OrganizationController::class)
        ->only(['index', 'store', 'update']);
    Route::get('/wallet', WalletController::class)->name('wallet.index');
    Route::resource('campaigns', CampaignController::class)
        ->only(['index', 'store', 'update']);
});

Route::webhooks('webhook-paynamics-paybiz', 'paynamics-paybiz');
