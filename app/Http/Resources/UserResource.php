<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /* @var \App\Models\User $data */
        $data = $this;

        return [
            'id' => $data->id,
            'name' => $data->name,
            'email' => $data->email,
            'created_at' => $data->created_at->toDateTimeString(),
            'avatar' => $data->avatar,
            'introduction' => $data->introduction,
            'notification_count' => $data->notification_count,
            'last_actived_at' => $data->last_actived_at,
            'weixin_openid' => $data->weixin_openid,
        ];
    }
}
