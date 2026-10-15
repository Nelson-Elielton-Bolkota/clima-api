<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CityController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CityResource::collection(City::all());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $city = City::create($validated);

        return (new CityResource($city))
            ->response()
            ->setStatusCode(201);
    }

    public function show(City $city): CityResource
    {
        return new CityResource($city);
    }

    public function update(Request $request, City $city): CityResource
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|size:2',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
        ]);

        $city->update($validated);

        return new CityResource($city);
    }

    public function destroy(City $city): JsonResponse
    {
        $city->delete();

        return response()->json(null, 204);
    }
}
