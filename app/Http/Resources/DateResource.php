<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DateResource extends JsonResource
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
            'type' => $this->type,
            'as_written' => $this->as_written,
            'not_before' => $this->not_before,
            'not_after' => $this->not_after,
            'note' => $this->note,
            'json' => json_decode($this->json)
        ];
    }
}
