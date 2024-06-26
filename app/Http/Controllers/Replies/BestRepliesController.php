<?php

declare(strict_types=1);

namespace App\Http\Controllers\Replies;

use App\Models\Reply;
use App\Http\Controllers\Controller;

final class BestRepliesController extends Controller
{
    /**
     * Mark the best reply for a thread.
     *
     * @param  Reply $reply
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Reply $reply)
    {
        $this->authorize('update', $reply->thread);

        $reply->thread->markBestReply($reply);
    }
}
