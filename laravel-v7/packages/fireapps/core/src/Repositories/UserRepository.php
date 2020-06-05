<?php

namespace Fireapps\Core\Repositories;

use Fireapps\Core\Models\Shop;
use Fireapps\Core\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;

class UserRepository extends Base
{
    use AuthenticatesUsers;

    /**
     * @param $payload
     * @return mixed
     */
    function create($payload)
    {
        if (!$payload['email']) {
            return ['status' => false, 'message' => 'Payload invalid'];
        }

        //check user email or company email
        if ($this->findUserByEmail($payload['email'])) {
            return ['status' => false, 'message' => 'User email exist'];
        }

        try {
            $this->db->beginTransaction();
            $user = User::create([
                'email'    => $payload['email'],
                'password' => Hash::make($payload['password']),
            ]);
            if ($user) {
                $shop = Shop::find($payload['shop_id']);
                if (!$shop) {
                    return ['status' => false, 'message' => 'shop not found'];
                }
                if ($shop) {
                    $shop->update(['user_id' => $user->id]);
                }

                $user         = User::find($user->id);
                $user['shop'] = $shop->toArray();
                if ($user) {
                    $user->load('shops');
                }
            }
            $this->db->commit();
            return ['status' => true, 'data' => $user];
        } catch (\Exception $exception) {
            $this->db->rollBack();
            return ['status' => false, 'message' => 'Create error'];
        }
    }

    /**
     * @param  null  $shopId
     * @param  null  $appId
     * @return array
     */
    public function getUserShopInfo($shopId = null, $appId = null)
    {
        $selectField = [
            //            'users.id as users_id', 'users.name as users_name', 'users.email as users_email',
            'shops.id as shops_id', 'shops.name as shops_name', 'shops.internal_id as shops_internal_id',
            'shops.email as shops_email', 'shops.domain as shop_domain', 'shops.raw_domain as shops_raw_domain',
            'shops.user_id as shop_user_id', 'shops.iana_timezone as shops_timezone',
            'app_install.app_id', 'app_install.is_charge', 'app_install.charge_id',
            'app_install.app_plan', 'app_install.status', 'app_install.on_boarding',
            'sp_shop_settings.timezone as setting_timezone', 'sp_shop_settings.time_format', 'sp_shop_settings.date_format',
            'shops.country_code as shop_country_code',
        ];
        $shop        = $this->db->table('shops')
            ->join('app_install', 'shops.id', 'app_install.shop_id')
            ->leftJoin('sp_shop_settings', 'sp_shop_settings.shop_id', 'shops.id')
            //            ->leftJoin('users', 'users.id', 'shops.user_id')
            ->select($selectField)
            ->where('shops.id', $shopId)->where('app_install.app_id', $appId)->first();

        if (!$shop || !$shop->status) {
            return ['status' => false, 'message' => 'Shop not exist', 'code' => 401];
        }

        $shop = (array) $shop;

        $result = [
            'id'    => $shop['shop_user_id'],
            'name'  => $shop['shops_name'],
            'email' => $shop['shops_email'],
            'shop'  => [
                'id'           => $shop['shops_id'],
                'name'         => $shop['shops_name'],
                'internal_id'  => $shop['shops_internal_id'],
                'email'        => $shop['shops_email'],
                'domain'       => $shop['shop_domain'],
                'raw_domain'   => $shop['shops_raw_domain'],
                'user_id'      => $shop['shop_user_id'],
                'app_id'       => $shop['app_id'],
                'is_charge'    => $shop['is_charge'],
                'charge_id'    => $shop['charge_id'],
                'app_plan'     => $shop['app_plan'],
                'status'       => $shop['status'],
                'on_boarding'  => json_decode($shop['on_boarding']),
                'timezone'     => isset($shop['setting_timezone']) ? $shop['setting_timezone'] : $shop['shops_timezone'],
                'time_format'  => isset($shop['time_format']) ? $shop['time_format'] : '12',
                'date_format'  => phpToJsFormatDate($shop['date_format']),
                'country_code' => $shop['shop_country_code'],
            ],
        ];
        return [
            'status' => true,
            'data'   => $result,
        ];
    }

    /**
     * @param  null  $shopId
     * @param  null  $appId
     * @return array
     */
    public function getUserInfoLite($shopId = null, $appId = null)
    {
        $shop = Shop::find($shopId);
        if (!$shop) {
            return [
                'status' => false,
            ];
        }
        $shop = $shop->toArray();

        if (!$shop['user_id']) {
            return [
                'status' => true,
                'data'   => [
                    'shop' => [
                        'id'          => $shop['id'],
                        'internal_id' => $shop['internal_id'],
                        'user_id'     => $shop['user_id'],
                        'email'       => $shop['email'],
                    ],
                ],
            ];
        }

        $user = User::find($shop['user_id']);
        if (!$user) {
            return [
                'status' => false,
            ];
        }

        $user         = $user->only(['id', 'name', 'email']);
        $user['shop'] = [
            'id'          => $shop['id'],
            'internal_id' => $shop['internal_id'],
            'user_id'     => $shop['user_id'],
        ];
        return [
            'status' => true,
            'data'   => $user,
        ];
    }

    public function checkShopStatus($shopId, $appId)
    {
        $selectField = [
            //            'users.id as users_id', 'users.name as users_name', 'users.email as users_email',
            'shops.id as shops_id', 'shops.name as shops_name', 'shops.internal_id as shops_internal_id',
            'shops.email as shops_email', 'shops.domain as shop_domain', 'shops.raw_domain as shops_raw_domain',
            'shops.user_id as shop_user_id', 'app_install.app_id', 'app_install.is_charge', 'app_install.charge_id',
            'app_install.app_plan', 'app_install.status', 'app_install.on_boarding',
        ];
        $shop        = $this->db->table('shops')->join('app_install', 'shops.id', 'app_install.shop_id')
            //            ->leftJoin('users', 'users.id', 'shops.user_id')
            ->select($selectField)
            ->where('shops.id', $shopId)->where('app_install.app_id', $appId)->first();

        if (!$shop || !$shop->status) {
            return false;
        }

        $shop = (array) $shop;

        return [
            'id'    => $shop['shop_user_id'],
            'name'  => $shop['shops_name'],
            'email' => $shop['shops_email'],
            'shop'  => [
                'id'          => $shop['shops_id'],
                'name'        => $shop['shops_name'],
                'internal_id' => $shop['shops_internal_id'],
                'email'       => $shop['shops_email'],
                'domain'      => $shop['shop_domain'],
                'raw_domain'  => $shop['shops_raw_domain'],
                'user_id'     => $shop['shop_user_id'],
                'app_id'      => $shop['app_id'],
                'is_charge'   => $shop['is_charge'],
                'charge_id'   => $shop['charge_id'],
                'app_plan'    => $shop['app_plan'],
                'status'      => $shop['status'],
            ],
        ];
    }
}
