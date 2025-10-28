<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CriteriaController;
use App\Http\Controllers\Admin\SubCriteriaController;
use App\Http\Controllers\Admin\AlternativeController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CalculationController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\FuelTypeController;
use App\Http\Controllers\Admin\TransmissionTypeController;

Auth::routes();

Route::get('/404', function () {
    return response()->view('admin.errors.404', [], 404);
})->name('error.custom.404');

Route::get('/', [HomeController::class, 'index'])->middleware('guest')->name('home');

Route::post('/register', [UserController::class, 'register'])->name('register');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/delete-image', [UserController::class, 'deleteProfileImage'])->name('profile.delete_image');

    Route::middleware(['not_admin'])->group(function () {
        Route::get('/calculation', [CalculationController::class, 'calculationUser'])->name('calculation.user');
        Route::get('/moora/report', [CalculationController::class, 'downloadPDFUser'])->name('moora.download_pdf_user');
    });

    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/calculation', [CalculationController::class, 'calculation'])->name('calculation');
        Route::get('/moora/report', [CalculationController::class, 'downloadPDF'])->name('moora.download_pdf');

        Route::resource('/user', UserController::class)->names('user');
        Route::resource('/student', StudentController::class)->names('student');
        Route::resource('/major', MajorController::class)->names('major');
        Route::resource('/subject', SubjectController::class)->names('subject');
        Route::resource('/criteria', CriteriaController::class)->names('criteria');
        Route::resource('/sub-criteria', SubCriteriaController::class)->names('subcriteria');
        Route::resource('/alternative', AlternativeController::class)->names('alternative');
    });
});
