<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUlid;

// Saved transcript of one chatbot exchange (message + response).
class ChatbotLog extends Model
{
    use HasUlid;

    protected $fillable = ['user_id', 'message', 'response'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
