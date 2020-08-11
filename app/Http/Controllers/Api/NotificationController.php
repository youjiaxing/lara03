<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = \Auth::user()->notifications();
        if ($after = $request->input('after')) {
            $query->where('created_at', '>', Carbon::parse($after));
        }

        $notifications = $query->paginate();
        return $this->successResponse(Notification::collection($notifications));
    }

    public function stats()
    {
        $user = \Auth::user();
        $total = $user->notifications()->count();
        return $this->successResponse(
            [
                'unread_count' => $user->notification_count,
                'total' => $total,
            ]
        );
    }

    public function readAll()
    {
        $user = \Auth::user()->markAsRead();
        return $this->stats();
    }
}
