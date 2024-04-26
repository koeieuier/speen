<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard', ['categories' => Category::all()]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //articles
    Route::get('/article/{id}', [ArticleController::class, 'show'])->name('article.show');
    Route::delete('/article/{id}', [ArticleController::class, 'destroy'])->name('article.destroy');
    Route::patch('/article/{id}', [ArticleController::class, 'patch'])->name('article.patch');
    Route::post('/article/new', [ArticleController::class, 'new'])->name('article.new');
    Route::get('/articles', [ArticleController::class, 'all'])->name('article.all');
    
    //comments
    Route::post('/article/{id}/comment', [CommentController::class, 'create'])->name('comment.create');

    //images
    Route::post('/article/{id}/image', [ImageController::class, 'create'])->name('image.create');

    //categories
    Route::post('/article/{id}/category', [CategoryController::class, 'create'])->name('category.create');
});

require __DIR__.'/auth.php';
