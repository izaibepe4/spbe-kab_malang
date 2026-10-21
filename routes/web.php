<?php

use App\Http\Controllers\AgendaPublikController;
use App\Http\Controllers\ArsitekturSpbeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\BeritaSpbeController;
use App\Http\Controllers\FaviconController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformasiSpbeController;
use App\Http\Controllers\InfoSpbePublikController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PaparanPublikController;
use App\Http\Controllers\PengetahuanPublikController;
use App\Http\Controllers\PublicDocumentController;
use App\Http\Controllers\RegulasiPublikController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SopPublikController;
use Illuminate\Support\Facades\Route;

// Public Home/Beranda
Route::get('/', [HomeController::class, 'index'])->name('public.home');

// Berita Kab Malang
Route::get('/berita-kab-malang', [BeritaSpbeController::class, 'index'])->name('berita-kab-malang.index');
Route::get('/berita-kab-malang/{slug}', [BeritaSpbeController::class, 'show'])->name('berita-kab-malang.show');

// Redirect route lama untuk backward compatibility
Route::get('/berita-spbe', function () {
    return redirect()->route('berita-kab-malang.index', request()->query());
})->name('berita-spbe.index');
Route::get('/berita-spbe/{slug}', function ($slug) {
    return redirect()->route('berita-kab-malang.show', $slug);
})->name('berita-spbe.show');

// Informasi SPBE
Route::get('/informasi-spbe', [InformasiSpbeController::class, 'index'])->name('informasi-spbe.index');
Route::get('/informasi-spbe/{slug}', [InformasiSpbeController::class, 'show'])->name('informasi-spbe.show');
Route::get('/informasi-spbe/progres/{id}/view', [InformasiSpbeController::class, 'viewProgresPdf'])->name('public.informasi.progres.view');

// Pengetahuan SPBE
Route::get('/pengetahuan-spbe', [PengetahuanPublikController::class, 'index'])->name('public.pengetahuan.index');
Route::get('/pengetahuan-spbe/{id}', [PublicDocumentController::class, 'pengetahuan'])->name('public.pengetahuan.view');

// Regulasi SPBE
Route::get('/regulasi-spbe', [RegulasiPublikController::class, 'index'])->name('public.regulasi.index');
Route::get('/regulasi-spbe/{id}/view', [RegulasiPublikController::class, 'viewPdf'])->name('public.regulasi.view');

// SOP SPBE
Route::get('/sop-spbe', [SopPublikController::class, 'index'])->name('public.sop.index');
Route::get('/sop-spbe/{id}/view', [SopPublikController::class, 'viewPdf'])->name('public.sop.view');

// Paparan SPBE
Route::get('/paparan-spbe', [PaparanPublikController::class, 'index'])->name('public.selengkapnya.paparan');
Route::get('/paparan-spbe/{id}/view', [PaparanPublikController::class, 'viewPdf'])->name('public.paparan.view');

// Agenda
Route::get('/agenda', [AgendaPublikController::class, 'index'])->name('public.selengkapnya.agenda');

// Arsitektur SPBE
Route::get('/arsitektur-spbe', [ArsitekturSpbeController::class, 'index'])->name('public.arsitektur_spbe');

// Info SPBE
Route::get('/info-spbe', [InfoSpbePublikController::class, 'index'])->name('public.info_spbe');

// Beranda (alternatif)
Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('public.search')->middleware('throttle:60,1');
// Rate limit: 60 requests per minute untuk search API
Route::get('/api/search', [SearchController::class, 'api'])->name('api.search')->middleware('throttle:60,1');

// Visitor tracking
Route::post('/visitor/record-gender', [\App\Http\Controllers\VisitorController::class, 'recordGender'])->name('visitor.record-gender');
Route::get('/visitor/check-gender', [\App\Http\Controllers\VisitorController::class, 'checkGenderRecorded'])->name('visitor.check-gender');
Route::post('/visitor/clear-session', [\App\Http\Controllers\VisitorController::class, 'clearSession'])->name('visitor.clear-session');

// Favicon
Route::get('/favicon.png', [FaviconController::class, 'favicon'])->name('favicon');
Route::get('/favicon-16x16.png', [FaviconController::class, 'favicon16'])->name('favicon.16');
Route::get('/favicon.ico', [FaviconController::class, 'faviconIco'])->name('favicon.ico');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // Rate limit: 5 attempts per minute untuk login
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    // Rate limit: 3 attempts per minute untuk register
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:3,1');
    Route::get('/register/success', [RegisterController::class, 'success'])->name('register.success');

    // Password Reset Routes
    Route::get('/password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    // Rate limit: 3 attempts per minute untuk password reset request
    Route::post('/password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Logout Routes - Support both GET and POST for better UX
Route::middleware(\App\Http\Middleware\AuthenticateWebOrAdmin::class)->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // GET route untuk handle direct access (misalnya dari bookmark atau link)
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');
});

// Protected Routes (Layanan)
Route::middleware([\App\Http\Middleware\AuthenticateWebOrAdmin::class, \App\Http\Middleware\CheckApproval::class])->group(function () {
    Route::get('/dashboard', [LayananController::class, 'index'])->name('dashboard');
    Route::resource('layanan', LayananController::class);

    // Rate limit untuk export (jika ada route export)
    // Route::get('/layanan/export', [LayananController::class, 'export'])->middleware('throttle:10,1');
});
