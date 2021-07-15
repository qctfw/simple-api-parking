<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ParkingHistoryRepositoryInterface
{
    public function all(): Collection;
    public function add(Collection $history);
    public function update(Collection $history);
    public function getParkedVehicle(string $plat);
    public function isVehicleParked(string $plat);
}
