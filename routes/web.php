<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/',[LoginController::class, 'showLogin'])->name('login');
Route::get('/verify/email/{user_id}',[LoginController::class, 'showEmailVerify'])->name('validationView')->middleware('signed')->where('user_id', '[0-9]+');
Route::get('/register', [LoginController::class, 'showRegister']);
Route::get('/home/user', [LoginController::class, 'showHomeUser'])->middleware('auth', 'active');
Route::get('/home/admin', [LoginController::class, 'showHomeAdmin'])->middleware('admin','auth', 'active');
Route::get('/verify/code/{user_id}', [LoginController::class, 'showVerifyCode'])->where('user_id', '[0-9]+');
Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth');
Route::get('/mailView', [LoginController::class, 'showMailView'])->name('mailView');

Route::post('/verify/code/{user_id}', [LoginController::class, 'verifyCode'])->name('validationCode')->middleware('signed')->where('user_id', '[0-9]+');
Route::post('/login/user', [LoginController::class, 'loginUser'])->middleware('throttle:login_attempts');
Route::post('/createUser',[LoginController::class, 'createUser']);
Route::post('/verify/user',[LoginController::class, 'verifyLogin']);
Route::post('/verify/email/{user_id}',[LoginController::class, 'verifyEmail'])->name('validationMail')->middleware('signed')->where('user_id', '[0-9]+');