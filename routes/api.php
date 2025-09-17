<?php

use App\Http\Controllers\Api\V1\Auth\AccessTokenController;
use App\Http\Controllers\Api\V1\Auth\CheckAuthenticatedController;
use App\Http\Controllers\Api\V1\Auth\RefreshTokenController;
use App\Http\Controllers\Api\V1\Auth\SendOtpCodeController;
use App\Http\Controllers\Api\V1\Auth\SubmitIdentityController;
use App\Http\Controllers\Api\V1\Auth\UpdateIdentityController;
use App\Http\Controllers\Api\V1\Auth\UserMeController;
use App\Http\Controllers\Api\V1\Bid\BidListController;
use App\Http\Controllers\Api\V1\Bid\CancelBidController;
use App\Http\Controllers\Api\V1\Bid\SubmitTransportForBidController;
use App\Http\Controllers\Api\V1\DispatchUnitType\DispatchUnitTypeListController;
use App\Http\Controllers\Api\V1\Driver\DriverCheckVerifyBiometricController;
use App\Http\Controllers\Api\V1\Driver\DriverVerifyBiometricController;
use App\Http\Controllers\Api\V1\Station\LoadingLocationListController;
use App\Http\Controllers\Api\V1\Station\StationListController;
use App\Http\Controllers\Api\V1\Ticket\TicketReplyController;
use App\Http\Controllers\Api\V1\Ticket\TicketSubmitController;
use App\Http\Controllers\Api\V1\Transports\AvailableTransportsController;
use App\Http\Controllers\Api\V1\Transports\CurrentTransportsController;
use App\Http\Controllers\Api\V1\Transports\TransportDetailController;
use App\Http\Controllers\Api\V1\Transports\TransportListAssignToDriverController;
use App\Http\Controllers\Api\V1\Transports\TransportWaybillToPdfController;
use App\Http\Controllers\Api\V1\TransportTrackingLog\TransportTrackingLogSubmitLocationController;
use App\Http\Controllers\Api\V1\Vehicle\VehicleChangeStatusController;
use App\Http\Controllers\Api\V1\Vehicle\VehicleDeleteController;
use App\Http\Controllers\Api\V1\Vehicle\VehicleDetailController;
use App\Http\Controllers\Api\V1\Vehicle\VehicleListController;
use App\Http\Controllers\Api\V1\Vehicle\VehicleStoreController;
use App\Http\Controllers\Api\V1\Vehicle\VehicleUpdateController;
use App\Http\Controllers\Api\V1\VehicleType\VehicleTypeListController;
use App\Http\Controllers\Api\V1\Version\CurrentVersionController;
use App\Http\Controllers\Api\V1\Version\VersionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('send-otp', SendOtpCodeController::class)->middleware('throttle:3,5');
        Route::post('token', AccessTokenController::class);
        Route::post('refresh-token', RefreshTokenController::class);
        Route::get('check-authenticated', CheckAuthenticatedController::class);
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('me', UserMeController::class);
        Route::post('/auth/submit-identity', SubmitIdentityController::class)->name('drivers.store');
        Route::put('/auth/submit-information', UpdateIdentityController::class)->name('drivers.update');

        Route::prefix('drivers')->group(function () {
            Route::post('verify-biometric', DriverVerifyBiometricController::class)
                ->name('drivers.verify.biometric');
            Route::get('/check-verify-biometric/{id}', DriverCheckVerifyBiometricController::class)
                ->name('drivers.check.verify.biometric');
        });

        Route::prefix('vehicles')->group(function () {
            Route::get('/', VehicleListController::class);
            Route::post('/', VehicleStoreController::class)->name('vehicles.store');
            Route::get('{vehicle}', VehicleDetailController::class);
            Route::patch('{vehicle}/change-status', VehicleChangeStatusController::class);
            Route::delete('/{vehicle}', VehicleDeleteController::class);
            Route::put('/{vehicle}', VehicleUpdateController::class);
        });

        Route::prefix('transports')->group(function () {
            Route::get('/', TransportListAssignToDriverController::class);
            Route::get('/available', AvailableTransportsController::class);
            Route::get('/current-active', CurrentTransportsController::class);
            Route::get('/{transport}', TransportDetailController::class);
            Route::post('/{transport}/bids', SubmitTransportForBidController::class);
            Route::post('/{transport}/locations', TransportTrackingLogSubmitLocationController::class);
            Route::get('{transport}/waybill-pdf', TransportWaybillToPdfController::class);
        });

        Route::prefix('tickets')->group(function () {
            Route::post('/', TicketSubmitController::class);
            Route::post('/{ticket}/reply', TicketReplyController::class);
        });

        Route::prefix('bids')->group(function () {
            Route::get('/', BidListController::class);
            Route::delete('/{bid}', CancelBidController::class)->name('bids.cancel');
        });
    });

    Route::prefix('versions')->group(function () {
        Route::get('/', CurrentVersionController::class);
        Route::get('check', VersionController::class);
    });

    Route::get('/vehicle-types', VehicleTypeListController::class)->name('vehicle-types.list');

    Route::get('/dispatch-unit-type', DispatchUnitTypeListController::class);

    Route::prefix('stations')->group(function () {
        Route::get('/', StationListController::class)->name('stations.list');
        Route::get('/{station}/loading-locations', LoadingLocationListController::class);
    });
});
