<?php
/** read this link for all changes in user CRUD
https://docs.google.com/document/d/1dQGotVLWKT0ezYnV2vb81dl-eWqm8H3cVhIspm80FNs/edit#bookmark=id.senm0jrcaira
 */
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use LaratrustUserTrait;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

/**create an uuid and save in users table automatically */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
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
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];
    public function menu(){
        return $this->hasMany(menu::class);
    }
    public function article(){
        return $this->hasMany(article::class);
    }
    public function category(){
        return $this->hasMany(category::class);
    }
    public function EmailGroup(){
        return $this->hasMany(EmailGroup::class);
    }
    public function Page(){
        return $this->hasMany(Page::class);
    }
    public function product(){
        return $this->hasMany(product::class);
    }
    public function order()
    {
        return $this->hasMany(Order::class);
    }
    public function shop()
    {
        return $this->hasMany(Shop::class);
    }
}
