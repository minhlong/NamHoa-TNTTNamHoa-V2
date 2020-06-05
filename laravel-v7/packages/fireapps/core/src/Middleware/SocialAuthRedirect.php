<?php

namespace Fireapps\Core\Middleware;

use Closure;
use Firebase\JWT\JWT;

class SocialAuthRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $state = $request->input('state', '');
        try {
            $stage = toArray(JWT::decode($state, config('fireapps.common.jwt_token'), ['HS256']));
            if (empty($stage['token']) || empty($stage['socialType'])) {
                return response()->json(['status' => false, 'message' => 'Payload invalid']);
            }
            $payload = JWT::decode($stage['token'], config('fireapps.common.jwt_token'), ['HS256']);
            $request->request->set('userInfo', toArray($payload));
            $request->request->set('socialType', $stage['socialType']);
            $request->request->set('action', @$stage['action']);
            $request->request->set('socialId', @$stage['socialId']);
            return $next($request);

        } catch (\Exception $exception) {
            return response()->json(['status' => false, 'message' => 'Token invalid']);
        }
    }
}
