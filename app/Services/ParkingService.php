<?php

namespace App\Services;

use App\Repositories\Contracts\ParkingHistoryRepositoryInterface;
use App\Repositories\Contracts\ParkingLotRepositoryInterface;
use App\Services\Contracts\ParkingServiceInterface;
use Carbon\Carbon;

class ParkingService implements ParkingServiceInterface
{
    /**
     * @var ParkingHistoryRepositoryInterface
     */
    private $parking_history_repository;
    
    /**
     * @var ParkingLotRepositoryInterface
     */
    private $parking_lot_repository;

    public function __construct(ParkingHistoryRepositoryInterface $parking_history_repository, ParkingLotRepositoryInterface $parking_lot_repository)
    {
        $this->parking_history_repository = $parking_history_repository;
        $this->parking_lot_repository = $parking_lot_repository;
    }

    public function checkIn(string $plat, string $color, string $type)
    {
        $check_parked = $this->parking_history_repository->isVehicleParked($plat);

        if ($check_parked)
        {
            abort(422, 'Kendaraan ini sudah masuk ke tempat parkir');
        }

        $available_lot = $this->parking_lot_repository->findAvailableLot();

        if (is_null($available_lot))
        {
            abort(422, 'Tempat parkir penuh');
        }

        $parking_data = collect([
            'id' => uniqid(),
            'plat_nomor' => $plat,
            'tipe' => strtoupper($type),
            'warna' => strtolower($color),
            'parking_lot' => $available_lot,
            'tanggal_masuk' => now()->format('Y-m-d H:i'),
            'tanggal_keluar' => null,
            'jumlah_bayar' => 0
        ]);

        $this->parking_lot_repository->switch($available_lot);

        $this->parking_history_repository->add($parking_data);

        return $parking_data->only('plat_nomor', 'parking_lot', 'tanggal_masuk');
    }

    public function checkOut(string $plat)
    {
        $parked_vehicle = $this->parking_history_repository->getParkedVehicle($plat);

        if (is_null($parked_vehicle))
        {
            abort(422, 'Kendaraan ini belum masuk ke tempat parkir');
        }

        $check_out_date = now();
        $price = $this->getPrice($parked_vehicle['tipe'], Carbon::parse($parked_vehicle['tanggal_masuk']), $check_out_date);

        $parked_vehicle = $parked_vehicle->merge([
            'tanggal_keluar' => $check_out_date->format('Y-m-d H:i'),
            'jumlah_bayar' => $price
        ]);

        $this->parking_lot_repository->switch($parked_vehicle['parking_lot']);

        $this->parking_history_repository->update($parked_vehicle);

        return $parked_vehicle->only('plat_nomor', 'tanggal_masuk', 'tanggal_keluar', 'jumlah_bayar');
    }

    private function getPrice(string $type, Carbon $check_in_date, Carbon $check_out_date)
    {
        switch ($type) {
            case 'SUV':
                $initial_price = 25000;
                break;
            case 'MPV':
                $initial_price = 35000;
                break;
        }

        return $initial_price + ($initial_price / 20 * $check_in_date->diffInHours($check_out_date));
    }
}
