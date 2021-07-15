<?php

namespace App\Providers;

use App\Repositories\Contracts\ParkingHistoryRepositoryInterface;
use App\Repositories\Contracts\ParkingLotRepositoryInterface;
use App\Repositories\ParkingHistoryRepository;
use App\Repositories\ParkingLotRepository;
use App\Services\Contracts\ParkingServiceInterface;
use App\Services\ParkingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ParkingServiceInterface::class, ParkingService::class);

        $this->app->bind(ParkingHistoryRepositoryInterface::class, ParkingHistoryRepository::class);
        $this->app->bind(ParkingLotRepositoryInterface::class, ParkingLotRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
