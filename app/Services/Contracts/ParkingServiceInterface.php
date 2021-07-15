<?php

namespace App\Services\Contracts;

interface ParkingServiceInterface
{
    public function checkIn(string $plat, string $color, string $type);
    public function checkOut(string $plat);
}
