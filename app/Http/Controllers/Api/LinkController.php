<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Link as LinkResource;
use App\Models\Link;

class LinkController extends Controller
{
    function index()
    {
        $links = Link::make()->getAllCached();
        return $this->successResponse(LinkResource::collection($links));
    }
}
