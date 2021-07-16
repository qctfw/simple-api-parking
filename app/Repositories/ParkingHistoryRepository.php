<?php

namespace App\Repositories;

use App\Repositories\Contracts\ParkingHistoryRepositoryInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ParkingHistoryRepository implements ParkingHistoryRepositoryInterface
{
    const PARKING_HISTORY_FILENAME = 'parking-history.json';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Collection
     */
    private $histories;

    public function __construct()
    {
        $this->filesystem = Storage::disk('local');

        $this->histories = $this->constructData();
    }
    
    public function all(): Collection
    {
        return $this->histories;
    }

    public function add(Collection $history)
    {
        $this->histories->push($history);

        $this->save();
    }

    public function update(Collection $history)
    {
        $this->histories = $this->histories->map(function ($item) use ($history) {
            return ($history['id'] === $item['id']) ? $history : $item;
        });

        $this->save();
    }

    public function getParkedVehicle(string $plat)
    {
        return $this->histories->where('plat_nomor', $plat)->where('tanggal_keluar', null)->first();
    }

    public function isVehicleParked(string $plat)
    {
        return !is_null($this->getParkedVehicle($plat));
    }

    private function constructData()
    {
        $filename = self::PARKING_HISTORY_FILENAME;

        $file_path = 'data/' . $filename;

        if (!$this->filesystem->exists($file_path)) {
            $file_path = 'data/dummy/' . $filename;
        }

        $json = $this->filesystem->get($file_path);

        return collect(json_decode($json, true))->map(function ($item) {
            return collect($item);
        });
    }

    private function save()
    {
        $json = $this->histories->toJson(JSON_PRETTY_PRINT);

        return $this->filesystem->put('data/' . self::PARKING_HISTORY_FILENAME, $json);
    }
}
