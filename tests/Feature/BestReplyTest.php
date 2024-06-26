<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Channel, Reply, Thread, User};
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};

class BestReplyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
        //$this->withExceptionHandling();

        $this->signIn(User::factory()->create());

        $thread = Thread::factory()->create(['user_id' => 1]);
        $replies = Reply::factory()->count(2)->create(['thread_id' => $thread->id]);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-replies.store', [$replies[1]->id]));

        $this->assertTrue($replies[1]->fresh()->isBest());
        $this->assertAuthenticated('web');
    }

    /** @test */
    public function only_the_thread_creator_may_mark_a_reply_as_best()
    {
        $user = User::factory()->create();
        $thread = Thread::factory()->create(['user_id' => $user->id]);
        $replies = Reply::factory()->count(2)->create(['thread_id' => $thread->id]);

        $this->signIn($user);

        $this->postJson(
            route('best-replies.store', [$replies[1]->id])
        )->assertStatus(200);

        $this->assertTrue($replies[1]->fresh()->isBest());
        $this->assertAuthenticated('web');
    }

    /** @test */
    public function if_a_best_reply_is_deleted_then_the_thread_is_properly_updated_to_reflect_that()
    {
        $user = User::factory()->create();
        $this->signIn($user);

        $reply = Reply::factory()->create(['user_id' => $user->id]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('replies.destroy', $reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
        $this->assertAuthenticated('web');
    }
}
