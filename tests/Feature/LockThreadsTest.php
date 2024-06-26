<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Reply, Thread, User};
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};

class LockThreadsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function non_administrators_may_not_lock_threads()
    {
        $this->signIn(User::factory()->create());
        $thread = Thread::factory()->create();

        $this->expectException(HttpException::class);

        $this->post(route('locked-threads.store', $thread))
            ->assertStatus(403)
            ->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function administrators_can_lock_threads()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'name' => 'JohnDoe'
        ]);
        $this->signIn($user);

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create(["body" => 'Some reply']);

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertFalse($thread->fresh()->locked, 'Failed asserting that the thread was locked.');
    }

    /** @test */
    public function administrators_can_unlock_threads()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'name' => 'JohnDoe'
        ]);
        $this->signIn($user);

        $thread = Thread::factory()->create(['user_id' => auth()->id(), 'locked' => true]);

        $this->delete(route('locked-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->locked, 'Failed asserting that the thread was unlocked.');
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $thread =Thread::factory()->create(['locked' => true]);

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
