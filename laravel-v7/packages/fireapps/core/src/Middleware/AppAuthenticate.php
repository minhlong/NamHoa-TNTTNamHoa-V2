<?php


namespace Fireapps\Core\Middleware;

use Closure;
use Fireapps\Core\Repositories\UserRepository;
use Firebase\JWT\JWT;

class AppAuthenticate
{
    private $userRepository;
    private $appID;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->appID = env('APP_ID');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        $appId = getAppId($this->appID);

        if (!$appId['status']) {
            return response()->json($appId);
        }

        $appId = $appId['data'];

        try {
            $payload = JWT::decode($token, config('fireapps.common.jwt_token'), ['HS256']);
            $shopId = isset($payload->shop->id) ? $payload->shop->id : 0;
            $checkShop = $this->userRepository->checkShopStatus($shopId, $appId);
            if (!$checkShop) {
                return response()->json(['status' => false, 'message' => '', 'code' => 401], 401);
            }

            $request->request->set('userInfo', toArray($checkShop));
            return $next($request);
        } catch (\Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage(), 'code' => 401], 401);
        }
    }
}
