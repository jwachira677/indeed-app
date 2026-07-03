<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FineController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;

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
    Route::resource('savings', SavingController::class);
    Route::put('savings/{saving}/approve', [SavingController::class, 'approve'])->name('savings.approve');
    Route::put('savings/{saving}/reject', [SavingController::class, 'reject'])->name('savings.reject');
    Route::get('members/{member}/savings', [SavingController::class, 'memberSavings'])->name('members.savings');
    Route::get('savings/summary', [SavingController::class, 'summary'])->name('savings.summary');
    Route::get('savings/export', [SavingController::class, 'export'])->name('savings.export'); 
});


Route::middleware(['auth'])->group(function () {
    Route::resource('fines', FineController::class);
    Route::post('fines/{fine}/pay', [FineController::class, 'pay'])->name('fines.pay');
    Route::put('fines/{fine}/waive', [FineController::class, 'waive'])->name('fines.waive');
    Route::get('members/{member}/fines', [FineController::class, 'memberFines'])->name('members.fines');
    Route::get('fines/export', [FineController::class, 'export'])->name('fines.export');
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
});



Route::middleware(['auth'])->group(function () {
    // Loan Eligibility
    Route::get('/loans/eligibility', [LoanController::class, 'eligibilityForm'])
        ->name('loans.eligibility.form');
    Route::post('/loans/eligibility', [LoanController::class, 'checkEligibility'])
        ->name('loans.eligibility.check');

    // Loan Application
    Route::post('/loans/apply', [LoanController::class, 'apply'])->name('loans.apply');

    // Loan Type Pages
    Route::get('/loans/instant', [LoanController::class, 'instant'])->name('loans.instant');
    Route::get('/loans/ordinary', [LoanController::class, 'ordinary'])->name('loans.ordinary');

    // API Routes
    Route::get('/loans/duration-tiers', [LoanController::class, 'getDurationTiers'])->name('loans.duration-tiers');

    // Resource routes (this handles index, show, store, update, destroy)
    Route::resource('loans', LoanController::class)->only(['index', 'show']);

    // Additional custom routes
    Route::get('/loans/status/{status}', [LoanController::class, 'byStatus'])->name('loans.by-status');
    
    // Loan Actions
    Route::put('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::put('/loans/{loan}/activate', [LoanController::class, 'activate'])->name('loans.activate');
    Route::put('/loans/{loan}/cancel', [LoanController::class, 'cancel'])->name('loans.cancel');
    Route::put('/loans/{loan}/complete', [LoanController::class, 'complete'])->name('loans.complete');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Analytics
Route::get('/analytics', function () {
    return view('analytics');
})->name('analytics');



Route::get('/users/create', function () {
    return view('users.create');
})->name('users.create');

// Products
Route::get('/products', function () {
    return view('products.index');
})->name('products.index');

Route::get('/products/create', function () {
    return view('products.create');
})->name('products.create');

Route::get('/products/categories', function () {
    return view('products.categories');
})->name('products.categories');

// Orders
Route::get('/orders', function () {
    return view('orders.index');
})->name('orders.index');


// Settings
Route::get('/settings', function () {
    return view('settings');
})->name('settings');

// Help
Route::get('/help', function () {
    return view('help');
})->name('help');

// Profile
Route::get('/profile', function () {
    return view('profile.edit');
})->name('profile.edit');


Route::get('/', function () {
    return redirect()->route('dashboard');
});



// Ordinary Loans
Route::get('/loans/ordinary', function () {
    return view('loans.ordinary');
})->name('loans.ordinary');



require __DIR__.'/auth.php';
