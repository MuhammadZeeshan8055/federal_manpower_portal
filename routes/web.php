<?php

use App\Http\Controllers\ClientDocumentController;
use App\Http\Controllers\ClientPortal\HomeController as ClientPortalHomeController;
use App\Http\Controllers\ClientPortal\LoginController as ClientPortalLoginController;
use App\Http\Controllers\ClientPortal\RegisterController as ClientPortalRegisterController;
use App\Http\Controllers\ClientPrintController;
use App\Http\Controllers\ClientStatusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employer\HomeController as EmployerHomeController;
use App\Http\Controllers\Employer\LoginController as EmployerLoginController;
use App\Http\Controllers\Employer\RegisterController as EmployerRegisterController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\PaymentScreenshotController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalarySlipPrintController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\WorkspaceCountryController;
use App\Livewire\Attendance\StartShift;
use App\Livewire\ClientPortal\ApplicationForm;
use App\Livewire\Employer\ManageEmployerJobs;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebsiteController::class, 'home'])->name('website.home');
Route::get('/about-us', [WebsiteController::class, 'about'])->name('website.about');
Route::get('/contact-us', [WebsiteController::class, 'contact'])->name('website.contact');
Route::get('/jobs', [WebsiteController::class, 'jobs'])->name('website.jobs');
Route::get('/countries', [WebsiteController::class, 'countries'])->name('website.countries');
Route::get('/services', [WebsiteController::class, 'services'])->name('website.services');
Route::get('/employers', [WebsiteController::class, 'employers'])->name('website.employers');

Route::get('/status/{token}', [ClientStatusController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{32,64}')
    ->middleware('throttle:60,1')
    ->name('client.status');

Route::prefix('portal')->name('client-portal.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/register', [ClientPortalRegisterController::class, 'create'])->name('register');
        Route::post('/register', [ClientPortalRegisterController::class, 'store']);
        Route::get('/login', [ClientPortalLoginController::class, 'create'])->name('login');
        Route::post('/login', [ClientPortalLoginController::class, 'store']);
    });

    Route::middleware(['auth', 'client'])->group(function () {
        Route::get('/', ClientPortalHomeController::class)->name('home');
        Route::get('/application', ApplicationForm::class)->name('application');
        Route::post('/logout', [ClientPortalLoginController::class, 'destroy'])->name('logout');
    });
});

Route::prefix('employer')->name('employer.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/register', [EmployerRegisterController::class, 'create'])->name('register');
        Route::post('/register', [EmployerRegisterController::class, 'store']);
        Route::get('/login', [EmployerLoginController::class, 'create'])->name('login');
        Route::post('/login', [EmployerLoginController::class, 'store']);
    });

    Route::middleware(['auth', 'employer'])->group(function () {
        Route::get('/', EmployerHomeController::class)->name('home');
        Route::get('/jobs', ManageEmployerJobs::class)->name('jobs');
        Route::post('/logout', [EmployerLoginController::class, 'destroy'])->name('logout');
    });
});

Route::middleware(['auth', 'office'])->group(function () {

    // Check-in gate (no attendance.started — otherwise loop)
    Route::get('/attendance/start', StartShift::class)->name('attendance.start');

    Route::middleware('attendance.started')->group(function () {

        // Home
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Module  → /dashboard/clients
        Route::get('/dashboard/{module}', [DashboardController::class, 'index'])
            ->where('module', '[a-z0-9_\-]+')
            ->name('dashboard.module');

        // Feature → /dashboard/clients/list
        Route::get('/dashboard/{module}/{feature}', [DashboardController::class, 'index'])
            ->where('module', '[a-z0-9_\-]+')
            ->where('feature', '[a-z0-9_\-]+')
            ->name('dashboard.feature');

        Route::post('/workspace/country', [WorkspaceCountryController::class, 'update'])->name('workspace.country');

        Route::get('/clients/{client}/documents/{docKey}', [ClientDocumentController::class, 'show'])
            ->where('docKey', '[a-z0-9_]+')
            ->middleware('throttle:60,1')
            ->name('clients.documents.show');

        Route::get('/clients/{client}/photo', [ClientDocumentController::class, 'photo'])
            ->middleware('throttle:60,1')
            ->name('clients.photo.show');

        Route::get('/payments/{payment}/screenshot', [PaymentScreenshotController::class, 'show'])
            ->middleware('throttle:60,1')
            ->name('accounts.payments.screenshot');

        Route::get('/payments/{payment}/receipt', [PaymentReceiptController::class, 'print'])
            ->middleware('throttle:60,1')
            ->name('accounts.payments.receipt');

        Route::get('/salary-slips/{salarySlip}/print', [SalarySlipPrintController::class, 'print'])
            ->middleware('throttle:60,1')
            ->name('attendance.salary-slips.print');

        Route::get('/clients/{client}/print/preview', [ClientPrintController::class, 'preview'])
            ->name('clients.print.preview');

        Route::get('/clients/{client}/print', [ClientPrintController::class, 'print'])
            ->name('clients.print');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
