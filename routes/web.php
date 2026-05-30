<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\VideoController;
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

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');
Route::get('/books/{book:slug}/{chapter:slug}', [BookController::class, 'chapter'])->name('books.chapter');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');

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
    Route::resource('portfolio', Admin\PortfolioController::class)->parameters(['portfolio' => 'portfolioItem']);
    Route::resource('papers', Admin\PaperController::class);
    Route::post('gallery/upload', [Admin\GalleryController::class, 'upload'])->name('gallery.upload');
    Route::resource('gallery', Admin\GalleryController::class);
    Route::resource('videos', Admin\VideoController::class);

    Route::get('settings/security', [Admin\SettingsController::class, 'security'])->name('settings.security');
    Route::put('settings/security', [Admin\SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    Route::resource('categories', Admin\CategoryController::class);

    // Books + nested chapters
    Route::resource('books', Admin\BookController::class);
    Route::prefix('books/{book}')->name('books.')->group(function () {
        Route::get('chapters', [Admin\BookChapterController::class, 'index'])->name('chapters.index');
        Route::get('chapters/create', [Admin\BookChapterController::class, 'create'])->name('chapters.create');
        Route::post('chapters', [Admin\BookChapterController::class, 'store'])->name('chapters.store');
        Route::get('chapters/{chapter}/edit', [Admin\BookChapterController::class, 'edit'])->name('chapters.edit');
        Route::put('chapters/{chapter}', [Admin\BookChapterController::class, 'update'])->name('chapters.update');
        Route::delete('chapters/{chapter}', [Admin\BookChapterController::class, 'destroy'])->name('chapters.destroy');
        Route::post('chapters/{chapter}/move-up', [Admin\BookChapterController::class, 'moveUp'])->name('chapters.move-up');
        Route::post('chapters/{chapter}/move-down', [Admin\BookChapterController::class, 'moveDown'])->name('chapters.move-down');
    });

    Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');

    Route::get('resume', fn() => view('admin.resume'))->name('resume');

    Route::get('/tags', [Admin\TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [Admin\TagController::class, 'store'])->name('tags.store');
    Route::delete('/tags/{tag}', [Admin\TagController::class, 'destroy'])->name('tags.destroy');
});

require __DIR__.'/auth.php';
