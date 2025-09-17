<?php

namespace App\Providers;

use App\Repository\Bid\BidRepository;
use App\Repository\Bid\BidRepositoryInterface;
use App\Repository\DispatchUnitType\DispatchUnitTypeRepository;
use App\Repository\DispatchUnitType\DispatchUnitTypeRepositoryInterface;
use App\Repository\Driver\DriverRepository;
use App\Repository\Driver\DriverRepositoryInterface;
use App\Repository\DriverVehicleAssignment\DriverVehicleAssignmentRepository;
use App\Repository\DriverVehicleAssignment\DriverVehicleAssignmentRepositoryInterface;
use App\Repository\DriverVehicleAssignmentHistory\DriverVehicleAssignmentHistoryRepository;
use App\Repository\DriverVehicleAssignmentHistory\DriverVehicleAssignmentHistoryRepositoryInterface;
use App\Repository\KycLog\KycLogRepository;
use App\Repository\KycLog\KycLogRepositoryInterface;
use App\Repository\Shipment\ShipmentRepository;
use App\Repository\Shipment\ShipmentRepositoryInterface;
use App\Repository\Station\StationRepository;
use App\Repository\Station\StationRepositoryInterface;
use App\Repository\Ticket\TicketRepository;
use App\Repository\Ticket\TicketRepositoryInterface;
use App\Repository\TicketMessage\TicketMessageRepository;
use App\Repository\TicketMessage\TicketMessageRepositoryInterface;
use App\Repository\Transport\TransportRepository;
use App\Repository\Transport\TransportRepositoryInterface;
use App\Repository\TransportTrackingLog\TransportTrackingLogRepository;
use App\Repository\TransportTrackingLog\TransportTrackingLogRepositoryInterface;
use App\Repository\User\UserRepository;
use App\Repository\User\UserRepositoryInterface;
use App\Repository\Vehicle\VehicleRepository;
use App\Repository\Vehicle\VehicleRepositoryInterface;
use App\Repository\VehicleType\VehicleTypeRepository;
use App\Repository\VehicleType\VehicleTypeRepositoryInterface;
use App\Repository\Version\VersionRepository;
use App\Repository\Version\VersionRepositoryInterface;
use App\Services\ThirdParties\BiometricService;
use App\Services\ThirdParties\Contracts\BiometricVerificationInterface;
use App\Services\ThirdParties\Contracts\PhoneNumberVerificationInterface;
use App\Services\ThirdParties\Contracts\VehicleVerificationInterface;
use App\Services\ThirdParties\HooshmandService;
use App\Services\ThirdParties\ShahkarService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PhoneNumberVerificationInterface::class, ShahkarService::class);
        $this->app->bind(BiometricVerificationInterface::class, BiometricService::class);
        $this->app->bind(VehicleVerificationInterface::class, HooshmandService::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DriverRepositoryInterface::class, DriverRepository::class);
        $this->app->bind(KycLogRepositoryInterface::class, KycLogRepository::class);
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
        $this->app->bind(DriverVehicleAssignmentHistoryRepositoryInterface::class, DriverVehicleAssignmentHistoryRepository::class);

        $this->app->bind(DriverVehicleAssignmentRepositoryInterface::class, DriverVehicleAssignmentRepository::class);

        $this->app->bind(ShipmentRepositoryInterface::class, ShipmentRepository::class);
        $this->app->bind(StationRepositoryInterface::class, StationRepository::class);
        $this->app->bind(VehicleTypeRepositoryInterface::class, VehicleTypeRepository::class);
        $this->app->bind(DispatchUnitTypeRepositoryInterface::class, DispatchUnitTypeRepository::class);

        $this->app->bind(VersionRepositoryInterface::class, VersionRepository::class);

        $this->app->bind(BidRepositoryInterface::class, BidRepository::class);
        $this->app->bind(TransportRepositoryInterface::class, TransportRepository::class);

        $this->app->bind(TransportTrackingLogRepositoryInterface::class, TransportTrackingLogRepository::class);
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(TicketMessageRepositoryInterface::class, TicketMessageRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Passport::enablePasswordGrant();
        Passport::tokensExpireIn(now()->addHours(5));
        Passport::refreshTokensExpireIn(now()->addHours(6));
    }
}
