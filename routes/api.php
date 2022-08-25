<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ForgotPasswordController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ReferenceController;
use App\Http\Controllers\Api\V1\GiftController;
use App\Http\Controllers\Api\V1\ContractorController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\BankController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('forgot_password', [ForgotPasswordController::class, 'forgot_password']);
    Route::post('update_password', [ForgotPasswordController::class, 'update_password']);
    Route::post('verify_otp', [ForgotPasswordController::class, 'verifyOtp']);
});

Route::group(['middleware' => ['jwt.verify']], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('references/add', [ReferenceController::class, 'addReference']);
    Route::get('stores', [ReferenceController::class, 'listStores']);
    Route::get('references/statuses', [ReferenceController::class, 'listStatuses']);
    Route::post('references', [ReferenceController::class, 'listReferences']);
    Route::post('references/update/{id}', [ReferenceController::class, 'updateReference']);
    Route::get('references/delete/{id}', [ReferenceController::class, 'deleteReference']);
    Route::get('references/details/{id}', [ReferenceController::class, 'referenceDetails']);

    Route::post('gifts', [GiftController::class, 'listGifts']);
    Route::post('cart', [GiftController::class, 'getCartInfo']);

    Route::get('points', [ContractorController::class, 'getTotalPoints']);
    Route::post('profile/update', [ContractorController::class, 'updateProfile']);
    Route::post('profile/upload_image', [ContractorController::class, 'uploadProfilePic']);
    Route::get('profile', [ContractorController::class, 'getProfile']);
    Route::post('profile/change_password', [ContractorController::class, 'changePassword']);
    Route::post('profile/push_notifications', [ContractorController::class, 'pushNotifications']);
    Route::get('notifications', [ContractorController::class, 'getNotifications']);
    Route::get('leaders-boards', [ContractorController::class, 'leaderBoard']);
    Route::delete('delete-account', [ContractorController::class, 'deleteAccount']);

    Route::post('banks/add', [BankController::class, 'addBank']);
    Route::post('banks/update/{id}', [BankController::class, 'updateBank']);
    Route::get('banks', [BankController::class, 'listBanks']);
    Route::get('banks/delete/{id}', [BankController::class, 'deleteBank']);

    Route::post('checkout', [OrderController::class, 'createOrder']);
    Route::post('points/redeem', [OrderController::class, 'addRedeemRequest']);
    Route::post('points/history', [OrderController::class, 'getPointsHistory']);
    Route::get('points/history/details/{id}', [OrderController::class, 'getPointHistoryDetails']);
});
