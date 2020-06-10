<?php

namespace TNTT\Middleware;

use Closure;
use Illuminate\Http\Request;
use TNTT\Models\LopHoc;
use TNTT\Models\TaiKhoan;

class CheckTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var TaiKhoan $user */
        /** @var LopHoc $lopHoc */
        $lopHoc = $request->route('lopHoc');
        $hts    = $lopHoc->huynh_truong()->get()->pluck('id');
        $user   = auth()->user();

        if (!$user->can('Lớp Học') && !$hts->contains($user->id)) {
            abort(403);
        }

        return $next($request);
    }
}
