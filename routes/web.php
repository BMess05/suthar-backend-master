<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\GiftController;
use App\Http\Controllers\Admin\ContractorController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Admin\ReferenceController;
use App\Http\Controllers\Admin\OrderController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/generate_password', [CommonController::class, 'generatePassword'])->name('generatePassword');

    /* Contractors routes */
    Route::get('users', [ContractorController::class, 'list'])->name('listContractors');


    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');

    Route::get('references/list', [ReferenceController::class, 'list'])->name('listReferences');

    Route::get('references/view/{id}', [ReferenceController::class, 'view'])->name('viewReference');


    Route::get('redeem_requests', [OrderController::class, 'listRedeenRequests'])->name('listRedeenRequests');

    /* Gifts routes */
    Route::get('gifts', [GiftController::class, 'list'])->name('listGifts');
    Route::get('gifts/add', [GiftController::class, 'add'])->name('addGift');
    Route::post('gifts/save', [GiftController::class, 'save'])->name('saveGift');
    Route::get('gifts/edit/{id}', [GiftController::class, 'edit'])->name('editGift');
    Route::post('gifts/update/{id}', [GiftController::class, 'update'])->name('updateGift');
    Route::get('gifts/delete/{id}', [GiftController::class, 'delete'])->name('deleteGift');

    Route::get('points_history', [OrderController::class, 'pointHistory'])->name('pointHistory');
    Route::post('getManagerStores', [UserController::class, 'getManagerStores'])->name('getManagerStores');

    Route::get('users/view/{id}', [ContractorController::class, 'view'])->name('viewContractor');

    Route::get('/exportUsers', [ContractorController::class, 'export'])->name('exportUsers');
    Route::middleware(['manager-only'])->group(function () {
        Route::get('users/add', [ContractorController::class, 'add'])->name('addContractor');
        Route::post('users/save', [ContractorController::class, 'save'])->name('saveContractor');
        Route::get('users/edit/{id}', [ContractorController::class, 'edit'])->name('editContractor');
        Route::post('users/update/{id}', [ContractorController::class, 'update'])->name('updateContractor');
        Route::get('users/delete/{id}', [ContractorController::class, 'delete'])->name('deleteContractor');
        Route::post('users/block-unblock', [ContractorController::class, 'blockUnblock'])->name('blockUnblock');

        Route::post('users/password-reset', [ContractorController::class, 'resetPassword'])->name('contractorPasswordReset');
        Route::post('references/change_status', [ReferenceController::class, 'changeStatus'])->name('refChangeStatus');
        Route::post('references/add_points', [ReferenceController::class, 'addPoints'])->name('refAddPoints');
        Route::post('redeem_requests/change_status', [OrderController::class, 'changeStatusRedeemRequest'])->name('changeStatusRedeemRequest');
        // Route::post('redeem_requests/revert_status', [OrderController::class, 'revertStatusRedeemRequest'])->name('revertStatusRedeemRequest');
    });
    Route::middleware(['admin-only'])->group(function () {
        /* Marketting users routes */
        Route::get('managers', [UserController::class, 'listUsers'])->name('listUsers');
        Route::get('managers/add', [UserController::class, 'add'])->name('addUser');
        Route::post('managers/save', [UserController::class, 'save'])->name('saveUser');
        Route::get('managers/edit/{id}', [UserController::class, 'edit'])->name('editUser');
        Route::post('managers/update/{id}', [UserController::class, 'update'])->name('updateUser');
        Route::get('managers/delete/{id}', [UserController::class, 'delete'])->name('deleteUser');
        Route::get('managers/view/{id}', [UserController::class, 'view'])->name('viewUser');
        Route::post('managers/active-inactive', [UserController::class, 'activeInactiveUser'])->name('activeInactiveUser');

        /* Stores routes */
        Route::get('stores', [StoreController::class, 'list'])->name('listStores');
        Route::get('stores/add', [StoreController::class, 'add'])->name('addStore');
        Route::post('stores/save', [StoreController::class, 'save'])->name('saveStore');
        Route::get('stores/edit/{id}', [StoreController::class, 'edit'])->name('editStore');
        Route::post('stores/update/{id}', [StoreController::class, 'update'])->name('updateStore');
        Route::get('stores/delete/{id}', [StoreController::class, 'delete'])->name('deleteStore');
    });
});


require __DIR__ . '/auth.php';
Route::get('privacy-policy', function() {
    return view('privacy-policy');
})->middleware('guest');

Route::get('terms-and-conditions', function() {
    return view('terms-and-conditions');
})->middleware('guest');
// ->middleware('guest')
// https://manvikdoorframes.com/
