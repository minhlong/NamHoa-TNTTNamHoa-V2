<?php


namespace Fireapps\Core\Repositories;

use Fireapps\Core\Models\AppInstall;
use Fireapps\Core\Models\Shop;
use Fireapps\Core\Models\User;
use Illuminate\Support\Facades\DB;

class Base
{
    protected $db;

    public function __construct()
    {
        $this->db = DB::connection(config('fireapps.core_db_connections'));
    }

    /**
     * @param $shopId
     * @param $platform
     * @return mixed
     */
    protected function findShop($shopId, $platform)
    {
        return Shop::where('internal_id', $shopId)->where('platform', $platform)->first();
    }

    /**
     * @param $appId
     * @param $shopId
     * @return mixed
     */
    protected function findInstallApp($appId, $shopId)
    {
        return AppInstall::where('app_id', $appId)->where('shop_id', $shopId)->first();
    }

    /**
     * @param $email
     * @return mixed
     */
    protected function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
