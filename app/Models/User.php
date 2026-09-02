<?php
/** read this link for all changes in user CRUD
https://docs.google.com/document/d/1dQGotVLWKT0ezYnV2vb81dl-eWqm8H3cVhIspm80FNs/edit#bookmark=id.senm0jrcaira
 */
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use LaratrustUserTrait;
    use HasFactory;
    use Notifiable;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
            $randomBytes = random_bytes(4);
            $randomString = bin2hex($randomBytes);
            $model->path = $randomString;
        });
    }

    public function getMorphClass()
    {
        return static::class;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function emailGroups()
    {
        return $this->hasMany(EmailGroup::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function baleConnections()
    {
        return $this->hasMany(ShopBaleConnection::class);
    }

    public function baleConnectionTokens()
    {
        return $this->hasMany(ShopBaleConnectionToken::class);
    }
}
