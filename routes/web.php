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

Route::get('test', function (\Illuminate\Http\Request $request) {
    $transaction_id = 1000008;
    $access_token = "Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBJZCI6IjEyZHFrbSIsImhhc2giOiJmYTFmZjU1ZTFiMmNlNmRiY2Q1OWQ1NDYxZmU2ZDAzODM4ZDA2MWU1NDQ1OWNhMGY1OTQxZTkyOTdlODY3YTVhIiwiaWF0IjoxNjc3MjI2OTEzLCJleHAiOjE2NzcyMjcyMTMsImp0aSI6IjI4NmZiN2Q0LThkZTItNDUwOC1iNDZjLTQ1YzdkM2RlODc3NyJ9.BjtPHKqGyDBpeMbmuGtp2epSnHZjc-bN3dv-R_HH67ideKsCQ4vHOUUuIkcVMK6qgMFz20BpcwtABTQBwr5mCI5Tylk0mQyFw532MOXoVdgTr52o8C453byb5EvjaTIMcdpwConkVMlom-KayQfYiX0Uiqmha6ke8Cf5B6Ywo2E";
    return view('hyperverge.test', [
        'transaction_id' => $transaction_id,
        'access_token' => $access_token
    ]);
});
