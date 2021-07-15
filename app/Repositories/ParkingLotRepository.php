<?php

namespace App\Repositories;

use App\Repositories\Contracts\ParkingLotRepositoryInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ParkingLotRepository implements ParkingLotRepositoryInterface
{
    const PARKING_LOT_FILENAME = 'parking-lot.json';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Collection
     */
    private $lots;

    public function __construct()
    {
        $this->filesystem = Storage::disk('local');

        $this->lots = $this->constructData();
    }
    
    public function all(): Collection
    {
        return $this->lots;
    }

    public function findAvailableLot(): ?string
    {
        return $this->lots->where('available', true)->first()['name'];
    }

    public function switch(string $name): bool
    {
        $this->lots = $this->lots->map(function ($item) use ($name) {
            return [
                'name' => $item['name'],
                'available' => ($item['name'] === $name) ? !$item['available'] : $item['available']
            ];
        });

        return $this->save();
    }

    private function constructData()
    {
        $filename = self::PARKING_LOT_FILENAME;

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
        $json = $this->lots->toJson(JSON_PRETTY_PRINT);

        return $this->filesystem->put('data/' . self::PARKING_LOT_FILENAME, $json);
    }
}
