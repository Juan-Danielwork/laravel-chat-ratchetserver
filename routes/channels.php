<?php

use App\Models\Message;
use App\Models\Room;
use App\Models\RoomMembers;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/*Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});*/

Broadcast::channel('room', function ($user) {

    $rooms = Room::with('user')->latest()->get()->toArray();
    return [
        'user' => auth()->user(),
        'rooms' => $rooms,
    ];
});

Broadcast::channel('member.{roomId}', function ($user, $roomId) {

    $members = RoomMembers::with('user')->where('room_id', $roomId)->latest()->get()->toArray();
    return [
        'user' => auth()->user(),
        'members' => $members,
    ];
});

Broadcast::channel('message.{roomId}', function ($user, $roomId) {

    $messages = Message::with('user')->where('room_id', $roomId)->get()->toArray();
    return [
        'user' => auth()->user(),
        'messages' => $messages,
    ];
});
