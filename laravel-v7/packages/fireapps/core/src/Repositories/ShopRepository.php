<?php


namespace Fireapps\Core\Repositories;

use Fireapps\Core\Models\AppInstall;
use Fireapps\Core\Models\Shop;
use Fireapps\Core\Models\User;
use Fireapps\Core\Services\Api\AuthApi;
use Fireapps\Core\Services\Api\ShopApi;


class ShopRepository extends Base
{
    private $authApi, $shopApi, $userRepository;

    public function __construct(AuthApi $authApi, ShopApi $shopApi, UserRepository $userRepository)
    {
        parent::__construct();

        $this->authApi        = $authApi;
        $this->shopApi        = $shopApi;
        $this->userRepository = $userRepository;
    }

    /**
     * @param $internalId
     * @param $appId
     * @param $platform
     * @return bool
     */
    function uninstallApp($internalId, $appId, $platform)
    {
        $shop = Shop::where('internal_id', $internalId)->where('platform', $platform)->first();
        if (!$shop) {
            return false;
        }
        $appInstall = AppInstall::where('shop_id', $shop->id)->where('app_id', $appId)->first();
        if (!$appInstall) {
            return false;
        }

        return $appInstall->update(
            [
                'access_token' => null,
                'is_charge'    => false,
                'charge_id'    => null,
                'app_plan'     => null,
                'status'       => false,
            ]
        );
    }

    /**
     * @param $appId
     * @param $payload
     * @param $platform
     * @return
     * @author Dang Bui
     */
    private function createOrUpdateShopApp($appId, $payload, $platform)
    {
        try {
            $this->db->beginTransaction();
            $result = $this->createOrUpdateShop($payload, $platform);
            if ($result) {
                $this->createOrUpdateAppInstall($appId, $payload, $result);
                $this->db->commit();
                return ['status' => true, 'data' => $this->getShopApp($appId, $payload['internal_id'], 'shopify')['data']];
            }
        } catch (\Exception $exception) {
            $this->db->rollBack();
            throw new  \Exception($exception->getMessage());
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @param $payload
     * @param $platform
     * @return mixed
     */
    private function createOrUpdateShop($payload, $platform)
    {
        if ($shop = $this->findShop($payload['internal_id'], $platform)) {
            $shop->update($payload);
        } else {
            Shop::create($payload);
        }

        return $this->findShop($payload['internal_id'], $platform);
    }

    /**
     * @param $appId
     * @param $payload
     * @param $shop
     */
    private function createOrUpdateAppInstall($appId, $payload, $shop)
    {
        if ($appInstall = $this->findInstallApp($appId, $shop->id)) {
            $appInstall->update([
                'app_id'       => $appId,
                'shop_id'      => $shop->id,
                'status'       => true,
                'access_token' => $payload['access_token'],
                'app_version'  => isset($payload['app_version']) ? $payload['app_version'] : null,
            ]);
        } else {
            $data                = [
                'app_id'       => $appId,
                'shop_id'      => $shop->id,
                'status'       => true,
                'access_token' => $payload['access_token'],
                'app_version'  => isset($payload['app_version']) ? $payload['app_version'] : null,
            ];
            $data['on_boarding'] = $this->getOnBoarding($appId);

            AppInstall::create($data);
        }
    }

    protected function getOnBoarding($appId)
    {
        return [];
    }

    /**
     * @param $payload
     * @return bool
     */
    private function mergeUserToShop($payload)
    {
        //check user
        $user = User::where('email', $payload['email'])->first();
        if ($user) {
            $shop = Shop::find($payload['shop_id']);
            if (!$shop) {
                return false;
            }

            $shop->user_id = $user->id;
            if ($shop->save()) {
                return true;
            }

            return false;
        } else {
            unset($payload['id']);
            $payload['password'] = 'social_head_great';
            $response            = $this->userRepository->create($payload);
            if (!$response['status']) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $appId
     * @param $internalId
     * @param $platform
     * @return mixed
     */
    private function getShopApp($appId, $internalId, $platform)
    {
        $shop = $this->db->table('shops')->join('app_install', 'shops.id', 'app_install.shop_id')
            ->where('shops.internal_id', $internalId)->where('shops.platform', $platform)->where('app_id', $appId)->first();
        if (!$shop) {
            return ['status' => true, 'data' => null];
        }

        return ['status' => true, 'data' => (array) $shop];
    }

    /**
     * Auth handle shopify
     * @param $request
     * @param $appId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function authHandle($request, $appId)
    {
        $platform = 'shopify';
        $shop     = $request->input('shop');

        //verify request
        $verify_request = $this->authApi->setParameter($appId)->verifyRequest($request->all());
        if (!$verify_request) {
            return ['status' => false, 'message' => 'Verify request fail'];
        }

        //get access token
        $code         = $request->input('code');
        $access_token = $this->authApi->setParameter($appId, $shop)->getAccessToken($shop, $code);

        if (!$access_token['status']) {
            return $access_token;
        }
        //get shop api info
        $shopApi = $this->shopApi->setParameter($appId, $shop, $access_token['data']['access_token'])->get();
        if (!$shopApi['status']) {
            return $shopApi;
        }

        $access_token            = $access_token['data']['access_token'];
        $shopApi                 = $shopApi['data']['shop'];
        $shopApi['access_token'] = $access_token;
        //get shop database
        $shopDb = $this->getShopApp($appId, $shopApi['id'], $platform)['data'];

        if (!$shopDb || empty($shopDb['status']) || $shopDb['app_version'] !== config('app.app_version')) {
            $shopApi['internal_id'] = $shopApi['id'];
            $shopApi['platform']    = $platform;
            $shopApi['raw_domain']  = $shopApi['myshopify_domain'];
            $shopApi['app_version'] = config('app.app_version');
            $result                 = $this->createOrUpdateShopApp($appId, $shopApi, $platform);

            if (!$result['status']) {
                return ['status' => false, 'message' => 'Cannot add shop'];
            }

            //handle after auth app
            $this->afterAuthApp($appId, $result['data']);

            $shopDb = $result['data'];
        }

        //merger user to shop
        if ($shopDb && !$shopDb['user_id']) {
            $result = $this->mergeUserToShop($shopDb);
        }

        $user = $this->userRepository->getUserInfoLite($shopDb['shop_id'], $appId);

        if (!$user['status']) {
            return $user;
        }

        return ['status' => true, 'data' => generalToken($user['data'])];
    }

    /**
     * @param $appId
     * @param $shopInfo
     */
    protected function afterAuthApp($appId, $shopInfo)
    {
    }
}
