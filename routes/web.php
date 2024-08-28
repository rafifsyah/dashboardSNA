<?php

use App\Http\Controllers\AkreditasiLksController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\KarangTarunaController;
use App\Http\Controllers\LayananLksController;
use App\Http\Controllers\LksController;
use App\Http\Controllers\PsmController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TkskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

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
    return redirect()->route('login');
});

Route::group(['middleware' => ['PageGuard']], function () {

    /**
     * Login
     *
     * - show login.blade.php
     */
    Route::get('login', [LoginController::class, 'loginView'])->name('login');

    /**
     * Dashboard
     */
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('', [DashboardController::class, 'dashboardView'])->name('dashboard.main');
        Route::get('profile', [DashboardController::class, 'dashboardProfileView'])->name('dashboard.profile');
    });

    /**
     * Super Admin Page
     */
    Route::group(['middleware' => ['PageForSuperadmin']], function () {
        /**
         * Dashboard - User Master
         *
         * - show user.blade.php
         */
        // 1- show user.blade.php
        Route::get('user/main', [UserController::class, 'userMainView'])->name('user.main');
        // 2- show user-create-update.blade.php
        Route::get('user/create', [UserController::class, 'userCreateView'])->name('user.create');
        // 3- show user-create-update.blade.php
        Route::get('user/update/{id}', [UserController::class, 'userUpdateView'])->name('user.update');
    });

    /**
     * Logout
     *
     * - clear cookie jwt_token and redirect login
     */
    Route::get('logout', function () {
        Cookie::queue(Cookie::forget('jwt_token'));
        return redirect()->route('login');
    });
});

