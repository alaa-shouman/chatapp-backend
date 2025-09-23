<?php

namespace App\Http\Controllers;

use App\Models\chats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getChats()
    {
        if(Auth::check()){
            return chats::whereHas('chat_participants', function($query){
                $query->where('user_id', Auth::id());
            })->with('chat_participants.user')->get();
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

   
}
