<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\VerifyEmailController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth 
Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);
Route::get('products/total', [ProductController::class, 'total'])->name('product.total');
Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('add-cart/{id}', function($id) {

    $cart = session()->get('cart', []);  
    if(isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            "name" => "name".$id,
            "quantity" => 1,
        ];
    }
    session()->put('cart', $cart);

    $response = [
        'success' => true,
        'data'    => $cart,
        'message' => "message",
    ];
    return response()->json($response, 200);
});
Route::get('get-cart', function() {
    $cart = session()->get('cart', []);
    $response = [
        'success' => true,
        'data'    => $cart,
        'message' => "message",
    ];
    return response()->json($response, 200);
});
     
Route::middleware('auth:api')->group( function () {    
    // log out
    Route::post('logout', [AuthController::class, 'logout']);

    // Verify email
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed'])
    ->name('verification.verify');

    // Resend link to verify email
    Route::post('/email/verify/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // Product
    Route::post('products/dropzoneMedia', [ProductController::class, 'dropzoneMedia'])->name('products.media');
    Route::resource('products', ProductController::class)->except([
        'show'
    ]);;  
    
    // Change Avatar and Name
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/show', [ProfileController::class, 'show'])->name('profile.show');

    
    // DM
    Route::post('message/room', [MessageController::class, 'room'])->name('message.room');
    Route::post('message/store', [MessageController::class, 'store'])->name('message.store');
    Route::get('message/index', [MessageController::class, 'index'])->name('message.index');
    Route::get('message/show', [MessageController::class, 'show'])->name('message.show');
    Route::get('message/notification', [MessageController::class, 'notification'])->name('message.notification');
});
