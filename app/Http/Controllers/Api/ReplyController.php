<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ReplyRequest;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReplyController extends Controller
{
    public function store(ReplyRequest $request, Topic $topic)
    {
        $reply = Reply::make($request->only('content'));
        //     ->forceFill(
        //         [
        //             'user_id' => \Auth::id(),
        //             'topic_id' => $topic->id,
        //         ]
        //     );
        $reply->user()->associate(\Auth::user());
        $reply->topic()->associate($topic);
        if (!$reply->save()) {
            throw new \Exception("保存失败");
        }
        return $this->success(\App\Http\Resources\Reply::make($reply), "", 201);
    }

    public function index(Topic $topic)
    {
        $replies = $this->query($topic, null)
            ->paginate();
        return $this->success(\App\Http\Resources\Reply::collection($replies));
    }

    public function indexByUser(User $user)
    {
        $replies = $this->query(null, $user)
            ->paginate();
        return $this->success(\App\Http\Resources\Reply::collection($replies));
    }

    protected function query(Topic $topic = null, User $user = null)
    {
        $builder = QueryBuilder::for(Reply::class)
            ->allowedIncludes('user', 'topic', 'topic.user')
            ->allowedSorts('id');

        if ($topic) {
            $builder->where('topic_id', $topic->id);
        }

        if ($user) {
            $builder->where('user_id', $user->id);
        }

        return $builder;
    }

    public function destroy(Topic $topic, Reply $reply)
    {
        if ($topic->id != $reply->topic_id) {
            throw new BadRequestHttpException();
        }

        $this->authorize('destroy', $reply);
        $reply->delete();
        return $this->success([], "", 204);
    }
}
