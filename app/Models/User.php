<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Bavix\Wallet\Interfaces\Confirmable;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWalletFloat;
use Bavix\Wallet\Traits\CanConfirm;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallets;
use Bavix\Wallet\Traits\HasWallet;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements Wallet, Confirmable, WalletFloat
{
    use HasApiTokens, HasFactory, Notifiable, HasWallet, CanConfirm, HasWallets, HasWalletFloat;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
