<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Topic extends JsonResource
{
    /**
     * @var \App\Models\Topic Topic
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['category'] = Category::make($this->whenLoaded('category'));
        $data['user'] = User::make($this->whenLoaded('user'));

        return $data;
    }
}
