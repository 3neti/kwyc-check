<?php

namespace App\Models;

use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Fortify\TwoFactorAuthenticatable;
use MOIREI\Vouchers\Traits\CanRedeemVouchers;
use Illuminate\Notifications\Notifiable;
use Bavix\Wallet\Interfaces\Confirmable;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWalletFloat;
use Laravel\Jetstream\HasProfilePhoto;
use Bavix\Wallet\Traits\CanConfirm;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallets;
use Bavix\Wallet\Traits\HasWallet;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasTeams;
use App\Traits\Verifiable;

class User extends Authenticatable implements Wallet, Confirmable, WalletFloat
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasWallet, CanConfirm, HasWallets, HasWalletFloat;
    use CanRedeemVouchers;
    use Verifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'data',
        'uri'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => SchemalessAttributes::class,
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    protected $schemalessAttributes = [
        'data',
    ];

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function routeNotificationForEngageSpark()
    {
        $field = config('engagespark.notifiable.route');

        return $this->{$field};
    }

    public function getURIAttribute()
    {
        return $this->data['uri'];
    }

    public function setURIAttribute($value): self
    {
        $this->data['uri'] = $value;

        return $this;
    }
}
