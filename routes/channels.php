<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    Log::info('App.Models.User.{id} channel', ['user' => $user, 'id' => $id]);
    return (int) $user->id === (int) $id;
});


Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    Log::info('chat channel', ['user' => $user, 'chatId' => $chatId]);
    return true;
});
