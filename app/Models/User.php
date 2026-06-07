<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Models\ChatbotLog;
use App\Models\Concerns\HasUlid;

// User account for all roles (owner, cashier, customer).
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUlid, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'google_id',
        'avatar',
        'profile_photo',
        'role',
        'status',
        'cart_data',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Virtual `active` flag derived from the `status` enum, so views can use
     * $user->active (true when status === 'active').
     */
    public function getActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Best available profile picture URL for the avatar: a locally uploaded
     * photo wins, then the Google SSO avatar (already a full URL), otherwise
     * null so views fall back to the initial. Use $user->profile_picture_url.
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        if ($this->profile_photo) {
            return Storage::url($this->profile_photo);
        }

        return $this->avatar ?: null;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function chatbotLogs()
    {
        return $this->hasMany(ChatbotLog::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }
}
