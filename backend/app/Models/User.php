<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'phone',
        'active'
    ];

    /**
     * The attributes that should be visiable for serialization.
     *
     * @var array<int, string>
     */
    protected $visible = [
        'id',
        'name',
        'email',
        'remember_token',
        'created_at',
        'type'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public static function whereClient()
    {
        return self::query()->where('type', UserTypes::client->value);
    }
    protected function name(): Attribute
    {
        return  Attribute::make(set: fn (string $value) => ucfirst($value));
    }
    public function orders()
    {
        return  $this->hasMany(Order::class, 'created_by');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function details()
    {
        return $this->hasOne(ClientAddresses::class);
    }
}
