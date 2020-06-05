<?php

namespace Fireapps\Core\Models;

class Shop extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'internal_id', 'platform', 'raw_domain', 'domain', 'email', 'name', 'user_id',
        'country_code', 'currency','iana_timezone', 'country', 'phone', 'shop_owner', 'money_format',
        'money_with_currency_format', 'weight_unit', 'plan_name', 'password_enabled', 'has_storefront', 'force_ssl'
    ];

    protected $hidden = ['access_token'];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appInstall()
    {
        return $this->hasOne(AppInstall::class, 'shop_id', 'id');
    }


    // public function socialAccount()
    // {
    //     return $this->hasMany('App\Model\SpSocialAccount', 'shop_id', 'id');
    // }
    // public function autoPosts()
    // {
    //     return $this->hasMany('App\Model\SpAutoPost', 'shop_id', 'id');
    //
    // }
}
