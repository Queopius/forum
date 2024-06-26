<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Reply, User};
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\{DatabaseMigrations, RefreshDatabase};

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_favorite_anything()
    {
        $this->expectException(AuthenticationException::class);

        $response = $this->post(
            'replies/' . Reply::factory()->create()->id . '/favorites'
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();

        $reply = Reply::factory()->create();

        $this->post('replies/' . $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_can_unfavorite_a_reply()
    {
        $this->signIn();

        $reply = Reply::factory()->create();

        $reply->favorite();

        $this->delete('replies/' . $reply->id . '/favorites');

        $this->assertCount(0, $reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();

        $reply = Reply::factory()->create();

        try {
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        } catch (\Exception $e) {
            $this->fail('Did not expect to insert the same record set twice.');
        }

        $this->assertCount(1, $reply->favorites);
    }
}
