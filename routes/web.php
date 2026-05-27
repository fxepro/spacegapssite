<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaperController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// Convenience redirect so /login works in addition to /admin/login
Route::redirect('/login', '/admin/login')->name('login.redirect');

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{portfolioItem:slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

Route::get('/papers', [PaperController::class, 'index'])->name('papers.index');
Route::get('/papers/{paper:slug}', [PaperController::class, 'show'])->name('papers.show');

Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Admin routes — protected by auth
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', Admin\PostController::class);
    Route::resource('portfolio', Admin\PortfolioController::class);
    Route::resource('papers', Admin\PaperController::class);
    Route::resource('categories', Admin\CategoryController::class);

    Route::get('/tags', [Admin\TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [Admin\TagController::class, 'store'])->name('tags.store');
    Route::delete('/tags/{tag}', [Admin\TagController::class, 'destroy'])->name('tags.destroy');
});

require __DIR__.'/auth.php';
