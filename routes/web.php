<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilePreviewController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\PriceReferenceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('akun', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('akun/profil', [AccountController::class, 'updateProfile'])->name('account.profile');
    Route::put('akun/sandi', [AccountController::class, 'updatePassword'])->name('account.password');

    Route::redirect('/', '/projects');
    Route::resource('projects', ProjectController::class);

    Route::post('projects/{project}/files', [ProjectFileController::class, 'store'])->name('projects.files.store');
    Route::post('projects/{project}/files/drive', [ProjectFileController::class, 'storeFromDrive'])->name('projects.files.drive');
    Route::get('files/{file}/download', [ProjectFileController::class, 'download'])->name('files.download');
    Route::get('files/{file}/preview', [FilePreviewController::class, 'show'])->name('files.preview');
    Route::get('files/{file}/raw', [FilePreviewController::class, 'raw'])->name('files.raw');
    Route::delete('files/{file}', [ProjectFileController::class, 'destroy'])->name('files.destroy');

    Route::get('prices', [PriceReferenceController::class, 'index'])->name('prices.index');
    Route::post('prices', [PriceReferenceController::class, 'store'])->name('prices.store');
    Route::put('prices/{price}', [PriceReferenceController::class, 'update'])->name('prices.update');
    Route::delete('prices/{price}', [PriceReferenceController::class, 'destroy'])->name('prices.destroy');
});
