<?php

namespace App\Swagger\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BookingResource",
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'booking_id', type: 'integer', example: 1),
        new OA\Property(property: 'is_finished', type: 'boolean', example: true),
        new OA\Property(property: 'start_time', type: 'string', example: '2025-07-03 10:52:00'),
        new OA\Property(property: 'end_time', type: 'string', example: '2025-07-03 12:52:00')
    ],
    type: "object"
)]

#[OA\Schema(
    schema: "BookingResourceCollection",
    properties: [
        new OA\Property(
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/BookingResource", type: "object")
        )
    ]
)]
class BookingResource extends JsonResource
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
            'booking_id' => $this->booking_id,
            'is_finished' => $this->end_time < now(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }
}
