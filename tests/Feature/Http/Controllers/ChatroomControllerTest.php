<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ChatroomControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    /**
     * User login test
     *
     * @return void
     */
    public function test_index_found()
    {
        $response = $this->get(route('room.view'));

        $response->assertStatus(302);
    }

    /**
     * display room view success test
     * @return void
     */
    public function test_index_success()
    {
        $user = User::factory()->create();
        Auth::login($user);
        $this->actingAs($user)->get(route('room.view'))
           ->assertStatus(200)
           ->assertViewIs('chatroom.room');
    }

    /**
     * Room creation test
     * @return void
     */
    public function test_create_room_success()
    {
        $title = $this->faker->sentence;
        $user = User::factory()->create();
        Auth::login($user);
        $this->actingAs($user)->post(route('room.store'), [
            'title'  => $title,
        ])->assertStatus(302)->assertRedirect(route('room.view'));
    }

    /**
     * Message display test
     * @return void
     */
    public function test_message_view_success()
    {
        $user = User::factory()->has(Room::factory())->create();
        $room = $user->rooms;
        Auth::login($user);
        $this->actingAs($user)->get(route('room.message.view', collect($room)->first()))
            ->assertStatus(200)
            ->assertViewIs('chatroom.message');
    }

    /**
     * Message creation test
     * @return void
     */
    public function test_message_store_success()
    {
        $user = User::factory()->has(Room::factory())->create();
        $room = collect($user->rooms)->first();
        Auth::login($user);
        $this->actingAs($user)->post(route('room.message.store', $room), [
            'message' => $this->faker->sentence,
        ])
            ->assertStatus(302)
            ->assertRedirect(route('room.message.view',$room));
    }
}
