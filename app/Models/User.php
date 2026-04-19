<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'team_name',
        'total_payment',
        'additional_shirt_count',
        'country',
        'region',
        'province',
        'city',
        'barangay',
        'postal_code',
        'paymongo_checkout_session_id',
        'token',
        'transaction_status'
    ];

    public function getAuthPassword()
    {
        return $this->detailUser->first()?->password;
    }

    public function detailUser()
    {
        return $this->hasMany(DetailUser::class, 'user_id');
    }
}
