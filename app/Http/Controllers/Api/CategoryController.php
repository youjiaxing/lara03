<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Topic as TopicResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function index()
    {
        $categories = Category::all();
        return $this->successResponse(CategoryResource::collection($categories));
    }

    function show(Category $category, Request $request)
    {
        $topics = $category->topics()->with('category')->paginate();
        return $this->successResponse(TopicResource::collection($topics));
    }
}
