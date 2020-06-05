<?php

namespace Fireapps\Core\Models;

class AppInstall extends BaseModel
{
    protected $table = 'app_install';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_id', 'shop_id', 'is_charge', 'charge_id', 'app_plan', 'status', 'access_token', 'on_boarding', 'app_version'
    ];

    protected $casts = [
        'on_boarding' => 'array'
    ];

}
