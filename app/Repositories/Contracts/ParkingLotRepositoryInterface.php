<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ParkingLotRepositoryInterface
{
    public function all(): Collection;
    public function findAvailableLot(): ?string;
    public function switch(string $name): bool;
}
