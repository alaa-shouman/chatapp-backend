<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class chats extends Model
{
    protected $fillable = ['chat_name', 'chat_type','chat_id','created_by'];


    public function messages()
    {
        return $this->hasMany(Messages::class);
    }

    public function chat_participants()
    {
        return $this->hasMany(Chat_participants::class);
    }
}
