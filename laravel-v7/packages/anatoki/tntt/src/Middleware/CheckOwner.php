<?php

namespace TNTT\Middleware;

use Closure;
use GuzzleHttp\Middleware;
use TNTT\Models\TaiKhoan;

class CheckOwner
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
        /** @var TaiKhoan $taiKhoan */
        $taiKhoan = $request->route('taiKhoan');

        /** @var TaiKhoan $user */
        $user = auth()->user();

        if ( !$user->can('Tài Khoản') && $taiKhoan->id != $user->id) {
            abort(403);
        }

        return $next($request);
    }
}
