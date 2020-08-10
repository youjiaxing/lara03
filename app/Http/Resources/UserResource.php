<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $showSensitive = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);

        /* @var User $that*/
        $that = $this->resource;
        $data['last_actived_at'] = $that->last_actived_at->toDateTimeString();
        $data['bound_phone'] = $that->phone ? true : false;
        $data['bound_wechat'] = $that->weixin_openid ? true : false;
        $data['bound_email'] = $that->email ? true : false;

        if ($this->showSensitive) {
            $data['phone'] = $that->phone;
            $data['email'] = $that->email;
        }
        return $data;
    }

    public function showSensitive($show = true)
    {
        $this->showSensitive = $show;
        return $this;
    }
}
