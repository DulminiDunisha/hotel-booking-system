<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomSeasonalRateController;
use App\Http\Controllers\RoomAvailabilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/hotel-rooms', [RoomController::class, 'listing'])->name('hotel-rooms');

Route::get('/room/details/{id}', [RoomController::class, 'details'])->name('room.details');

Route::get('/room/booking', function () {
    return Inertia::render('BookingForm');
})->name('room.booking');

Route::get('/room/booking/confirmation', [BookingController::class, 'showConfirmation'])->name('room.booking.confirmation');

Route::post('/room/booking', [BookingController::class, 'storeGuestBooking'])->name('room.booking.store');

// Public API routes for booking form
Route::prefix('api')->group(function () {
    Route::get('/bookings/available-rooms', [App\Http\Controllers\Api\BookingApiController::class, 'getAvailableRooms']);
});

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

Route::get('/emergency', [EmergencyController::class, 'guestEmergencyForm'])->name('emergency.form');

Route::post('/emergency/submit', [EmergencyController::class, 'submitGuestEmergency'])->name('emergency.submit');

Route::get('/datepicker-demo', function () {
    return Inertia::render('DatePickerDemo');
})->name('datepicker.demo');

Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

// Payment routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/initiate', [PaymentController::class, 'initiatePayment'])->name('initiate');
    Route::get('/return', [PaymentController::class, 'paymentReturn'])->name('return');
    Route::get('/cancel', [PaymentController::class, 'paymentCancel'])->name('cancel');
    Route::post('/notify', [PaymentController::class, 'paymentNotify'])->name('notify');
    Route::get('/success', [PaymentController::class, 'paymentSuccess'])->name('success');
    Route::get('/error', [PaymentController::class, 'paymentError'])->name('error');
    Route::get('/status/{paymentId}', [PaymentController::class, 'getPaymentStatus'])->name('status');
    Route::post('/refund/{paymentId}', [PaymentController::class, 'refundPayment'])->name('refund');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/admin/users/management', function () {
        return Inertia::render('Admin/UserManagement');
    })->name('admin.users.management');

    Route::get('/admin/booking/management', [BookingController::class, 'index'])->name('admin.booking.management');

    Route::get('/admin/room/management', function () {
        return Inertia::render('Admin/RoomInventory');
    })->name('admin.room.management');

    Route::get('/admin/payment/hub', function () {
        return Inertia::render('Admin/PaymentHub');
    })->name('admin.payment.hub');

    Route::get('/admin/gallery/management', [GalleryController::class, 'adminIndex'])->name('admin.gallery.management');

    Route::get('/admin/emergency/cases', [EmergencyController::class, 'index'])->name('admin.emergency.cases');

    Route::get('/admin/emergency/cases/{id}', [EmergencyController::class, 'show'])->name('admin.emergency.cases.show');

    Route::resource('users', UserController::class);

    Route::resource('rooms', RoomController::class);

    // Custom route for room updates with FormData
    Route::post('/rooms/{room}/update', [RoomController::class, 'updateWithFormData'])->name('rooms.update.formdata');

    Route::resource('rooms.seasonal-rates', RoomSeasonalRateController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('rooms.availabilities', RoomAvailabilityController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('bookings', BookingController::class);

    // Emergency management routes
    Route::prefix('admin/emergency')->name('admin.emergency.')->group(function () {
        Route::post('/cancellation', [EmergencyController::class, 'createEmergencyCancellation'])->name('cancellation');
        Route::post('/illness', [EmergencyController::class, 'createIllnessEmergency'])->name('illness');
        Route::put('/{id}/resolve', [EmergencyController::class, 'resolveEmergencyCase'])->name('resolve');
        Route::post('/{id}/refund', [EmergencyController::class, 'processRefund'])->name('refund');
        Route::get('/statistics', [EmergencyController::class, 'getEmergencyStatistics'])->name('statistics');
    });

    // Gallery management routes
    Route::prefix('admin/gallery')->name('admin.gallery.')->group(function () {
        Route::post('/images', [GalleryController::class, 'store'])->name('images.store');
        Route::put('/images/{id}', [GalleryController::class, 'update'])->name('images.update');
        Route::delete('/images/{id}', [GalleryController::class, 'destroy'])->name('images.destroy');
        Route::post('/images/sort-order', [GalleryController::class, 'updateSortOrder'])->name('images.sort-order');
        Route::put('/images/{id}/featured', [GalleryController::class, 'toggleFeatured'])->name('images.toggle-featured');
        Route::put('/images/{id}/active', [GalleryController::class, 'toggleActive'])->name('images.toggle-active');
        Route::get('/images/category/{category}', [GalleryController::class, 'getImagesByCategory'])->name('images.by-category');
        Route::get('/images/featured', [GalleryController::class, 'getFeaturedImages'])->name('images.featured');
    });

    // API routes for booking management
    Route::prefix('api')->group(function () {
        Route::get('/bookings', [App\Http\Controllers\Api\BookingApiController::class, 'index']);
        Route::get('/bookings/guests', [App\Http\Controllers\Api\BookingApiController::class, 'getGuests']);
        Route::get('/bookings/rooms', [App\Http\Controllers\Api\BookingApiController::class, 'getRooms']);
        Route::get('/bookings/room-types', [App\Http\Controllers\Api\BookingApiController::class, 'getRoomTypes']);
        Route::get('/bookings/stats', [App\Http\Controllers\Api\BookingApiController::class, 'getStats']);
        
        // Room management API routes
        Route::get('/rooms', [App\Http\Controllers\Api\RoomApiController::class, 'index']);
        Route::get('/rooms/stats', [App\Http\Controllers\Api\RoomApiController::class, 'getStats']);
        Route::get('/rooms/types', [App\Http\Controllers\Api\RoomApiController::class, 'getRoomTypes']);
        Route::get('/rooms/available', [App\Http\Controllers\Api\RoomApiController::class, 'getAvailableRooms']);
        Route::get('/rooms/{room}', [App\Http\Controllers\Api\RoomApiController::class, 'show']);
    });

    Route::resource('calendar-events', CalendarEventController::class);

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
