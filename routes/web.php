<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GradeLevelController;
use App\Http\Controllers\SchoolClassController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fee\FeetypeController;
use App\Http\Controllers\Fee\FeeStructureController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\TransportRouteController;
use App\Http\Controllers\VehicleController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::put('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::put('/roles/{role}/activate', [RoleController::class, 'activate'])->name('roles.activate');
    Route::put('/roles/{role}/suspend', [RoleController::class, 'suspend'])->name('roles.suspend');
    Route::put('/roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
    Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');

    Route::resource('transport-routes', TransportRouteController::class);
    Route::resource('vehicles', VehicleController::class);



    Route::resource('fee-types', FeetypeController::class);
    Route::resource('fee-structures', FeeStructureController::class);

    Route::resource('grade-levels', GradeLevelController::class);
    Route::resource('classes', SchoolClassController::class);
    Route::resource('streams', StreamController::class);
    Route::resource('students', StudentController::class);

    Route::resource('enrollments', EnrollmentController::class);
    Route::get('students/{student}/enroll', [EnrollmentController::class, 'create'])->name('students.enroll');
    Route::post('students/{student}/enroll', [EnrollmentController::class, 'store'])->name('students.enroll.store');






        Route::get('permissions', [PermissionController::class, 'index'])
        ->middleware('can:permissions.view')->name('permissions');
    Route::put('permissions/{role}', [PermissionController::class, 'update'])
        ->middleware('can:permissions.update')->name('permissions.update');

});










require __DIR__.'/auth.php';
