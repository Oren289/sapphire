<?php

use App\Http\Controllers\BucketListItemController;
use App\Http\Controllers\GalleryPictureController;
use App\Http\Controllers\MoneyEntryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\SavingsDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/money-tracker', [MoneyEntryController::class, 'index'])->name('money.index');
    Route::post('/money-tracker', [MoneyEntryController::class, 'store'])->name('money.store');
    Route::get('/money-tracker/{moneyEntry}/edit', [MoneyEntryController::class, 'edit'])->name('money.edit');
    Route::patch('/money-tracker/{moneyEntry}', [MoneyEntryController::class, 'update'])->name('money.update');
    Route::delete('/money-tracker/{moneyEntry}', [MoneyEntryController::class, 'destroy'])->name('money.destroy');
    Route::get('/savings', [SavingsDashboardController::class, 'index'])->name('savings.index');
    Route::get('/gallery', [GalleryPictureController::class, 'index'])->name('gallery.index');
    Route::get('/gallery/{galleryPicture}/download', [GalleryPictureController::class, 'download'])->name('gallery.download');
    Route::post('/gallery', [GalleryPictureController::class, 'store'])->middleware('throttle:10,1')->name('gallery.store');
    Route::get('/gallery/{galleryPicture}/image', [GalleryPictureController::class, 'show'])->name('gallery.image');
    Route::get('/gallery/{galleryPicture}/edit', [GalleryPictureController::class, 'edit'])->name('gallery.edit');
    Route::patch('/gallery/{galleryPicture}', [GalleryPictureController::class, 'update'])->middleware('throttle:10,1')->name('gallery.update');
    Route::delete('/gallery/{galleryPicture}', [GalleryPictureController::class, 'destroy'])->name('gallery.destroy');
    Route::get('/bucket-list', [BucketListItemController::class, 'index'])->name('bucket-list.index');
    Route::post('/bucket-list', [BucketListItemController::class, 'store'])->name('bucket-list.store');
    Route::get('/bucket-list/{bucketListItem}/edit', [BucketListItemController::class, 'edit'])->name('bucket-list.edit');
    Route::patch('/bucket-list/{bucketListItem}', [BucketListItemController::class, 'update'])->name('bucket-list.update');
    Route::patch('/bucket-list/{bucketListItem}/fulfillment', [BucketListItemController::class, 'fulfill'])->name('bucket-list.fulfill');
    Route::delete('/bucket-list/{bucketListItem}', [BucketListItemController::class, 'destroy'])->name('bucket-list.destroy');
    Route::get('/profile/images/{type}', [ProfileImageController::class, 'show'])->whereIn('type', ['profile', 'background'])->name('profile.images.show');
    Route::post('/profile/images/{type}', [ProfileImageController::class, 'update'])->whereIn('type', ['profile', 'background'])->middleware('throttle:10,1')->name('profile.images.update');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
