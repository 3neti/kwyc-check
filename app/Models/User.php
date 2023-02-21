<?php

namespace App\Models;

use App\Actions\Fortify\CreateNewUser;
use App\Classes\Phone;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\TwoFactorAuthenticatable;
use MOIREI\Vouchers\Traits\CanRedeemVouchers;
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
use App\Traits\HasMobile;
use App\Traits\HasData;

use Illuminate\Support\Arr;

class User extends Authenticatable implements Wallet, Confirmable, WalletFloat
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use TwoFactorAuthenticatable;
    use HasWallet, CanConfirm, HasWallets, HasWalletFloat;
    use CanRedeemVouchers;
    use HasMobile;
    use Verifiable;
    use HasData;

    /**
     * The attributes that are mass assignable.
     *
     * @var string<int, string>
     */
    protected $fillable = [
        'name', 'email', 'mobile', 'password'
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
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    static public function getSystem(): User
    {
        $attribs = Arr::only(config('domain.seed.user.system'), ['email']);
//        Arr::set($attribs, 'email', decrypt($attribs['email']));

        return static::where($attribs)->first();
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot('active')
            ->withTimestamps();
    }

    static public function eurekaPersist(array $attribs, array $needles = ['email', 'mobile']): User
    {
        $validator = Validator::make($attribs, [
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validation fails');
        }

        $attribs = $validator->validated();
        foreach ($needles as $needle) {
            $attrib = $attribs[$needle];
            $user = match ($needle) {
                'mobile' => self::fromMobile($attrib),
                default => self::where($needle, $attrib)->first(),
            };
        }
        $attribs = array_merge($attribs, config('domain.default.user.attribs'));

        return $user ?? tap(app(CreateNewUser::class)->create($attribs), function (User $user) use ($attribs) {

            $user->setAttribute('mobile', $attribs['mobile']);
            $user->save();
        });
    }
}
