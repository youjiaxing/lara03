<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InternalException;
use App\Http\Requests\Api\TopicRequest;
use App\Http\Resources\Topic as TopicResource;
use App\Models\Topic;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TopicController extends Controller
{
    function index(User $user = null)
    {
        $builder = QueryBuilder::for(Topic::class)
            ->allowedIncludes('category', 'user')
            ->allowedFilters(
                [
                    'title',
                    AllowedFilter::exact('category_id'),
                    AllowedFilter::scope('withOrder')->default('recentReplied'),
                ]
            );
        if ($user) {
            $builder->where('user_id', $user->id);
        }

        $topics = $builder->paginate();

        return $this->success(TopicResource::collection($topics));
    }

    function indexByUser(User $user)
    {
        return $this->index($user);
    }

    function show($topicId)
    {
        $topic = QueryBuilder::for(Topic::class)
            ->allowedIncludes('user', 'category')
            ->findOrFail($topicId);
        return $this->success(\App\Http\Resources\Topic::make($topic));
    }

    function store(TopicRequest $request)
    {
        $data = $request->validated();
        $topic = Topic::make($data);
        $topic->user_id = \Auth::id();
        if (!$topic->save()) {
            throw new InternalException("创建 topic 失败");
        }
        return $this->success(\App\Http\Resources\Topic::make($topic), "", 201);
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $data = $request->validated();
        if (empty($data)) {
            throw new BadRequestHttpException("没有可更新的字段");
        }

        if (!$topic->update($data)) {
            throw new \Exception("更新失败");
        }

        return $this->success(\App\Http\Resources\Topic::make($topic));
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();
        return $this->success([], "", 204);
    }
}
