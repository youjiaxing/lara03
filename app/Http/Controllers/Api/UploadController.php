<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\UploadImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    function storeImage(UploadImageRequest $request, ImageUploadHandler $imageUploadHandler)
    {
        $user = auth('api')->user();
        $type = $request->input('type');

        $result = $imageUploadHandler->save($request->file('image'), Str::plural($type), $user->id, $type);
        $image = Image::forceCreate(
            [
                'user_id' => $user->id,
                'type' => $type,
                'filesystem' => Image::FILESYSTEM_LOCAL,
                'path' => $result['path'],
            ]
        );

        return $this->success(new ImageResource($image));
    }
}
