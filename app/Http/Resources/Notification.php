<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /**
         * @var DatabaseNotification $that
         */
        $that = $this->resource;

        $data = [
            'id' => $that->id,
            'type' => $that->type,
            'data' => $that->data,
            'read_at' => $that->read_at ?? null,
            'created_at' => $that->created_at->toDateTimeString(),
        ];

        return $data;
    }
}
