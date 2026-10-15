<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cidade' => $this->name,
            'uf' => strtoupper($this->state),
            'coordenadas' => [
                'lat' => (float) $this->latitude,
                'lon' => (float) $this->longitude,
            ],
            'cadastrado_em' => $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}
