<?php

use App\Http\Controllers\SwitchLocaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
|
| The landing page is the only public page. Login, registration, password
| reset and every signed-in screen live in the Filament panel (/dashboard),
| see App\Providers\Filament\DashboardPanelProvider.
|
*/

Route::view('/', 'welcome')->name('home');

Route::get('locale/{locale}', SwitchLocaleController::class)
    ->where('locale', '[a-z]{2}')
    ->name('locale.switch');

/*
| Friendly aliases so "/login" and "/register" work from anywhere.
*/
Route::redirect('login', '/dashboard/login')->name('login');
Route::redirect('register', '/dashboard/register')->name('register');
