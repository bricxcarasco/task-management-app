<?php

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

// Test Channels
Broadcast::channel('test.chat.room.{id}', function ($user) {
    return true;
}, ['guards' => ['web']]);

// Chat room private channel
Broadcast::channel('chat.room.{id}', function ($user) {
    return true;
}, ['guards' => ['web']]);

// Inquiry messages private channel
Broadcast::channel('inquiry.conversation.{id}', function ($user) {
    return true;
}, ['guards' => ['web']]);
