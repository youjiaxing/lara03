<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = \Auth::user()->getAllPermissions();
        return $this->success(Permission::collection($permissions));
    }
}
