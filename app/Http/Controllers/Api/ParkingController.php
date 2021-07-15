<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\ParkingServiceInterface;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    /**
     * @var ParkingServiceInterface
     */
    private $parking_service;

    public function __construct(ParkingServiceInterface $parking_service)
    {
        $this->parking_service = $parking_service;
    }

    public function checkIn(Request $request)
    {
        $validated = $this->validate($request, [
            'plat_nomor' => ['required', 'string'],
            'warna' => ['required', 'alpha'],
            'tipe' => ['required', 'in:SUV,MPV']
        ]);

        $check_in = $this->parking_service->checkIn($validated['plat_nomor'], $validated['warna'], $validated['tipe']);

        return response()->json($check_in->toArray(), 201);
    }

    public function checkOut(Request $request)
    {
        $validated = $this->validate($request, [
            'plat_nomor' => ['required', 'string']
        ]);

        $check_out = $this->parking_service->checkOut($validated['plat_nomor']);

        return response()->json($check_out->toArray(), 200);
    }

    public function getByPlat(Request $request)
    {
        # code...
    }

    public function getByColor(Request $request)
    {
        # code...
    }
}
