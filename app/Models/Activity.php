<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Fetch the associated subject for the activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Fetch an activity feed for the given user.
     *
     * @param  User $user
     * @param  int  $take
     *
     * @return \Illuminate\Database\Eloquent\Collection<int|string>;
     */
    public static function feed(User $user, $take = 50)
    {
        return static::whereUserId($user->id)
            ->latest()
            ->with('subject')
            ->take($take)
            ->get()
            ->groupBy(
                function ($activity) {
                return $activity->created_at->format('Y-m-d');
            }
            );
    }
}
