<?php

namespace TNTT\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TNTT\Models\KhoaHoc;
use TNTT\Models\ThuMoi;

class ThuMoiController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['bindings'])->only([
            'show',
            'update',
            'destroy',
        ]);
        $this->middleware(['can:Lớp Học'])->only([
            'store',
            'update',
            'destroy',
        ]);
    }

    /**
     * @param  ThuMoi  $thuMoi
     * @return Response
     */
    public function index(ThuMoi $thuMoi)
    {
        $khoaHocID = KhoaHoc::hienTai()->id;
        $thuMoi    = $thuMoi->locDuLieu()
            ->with([
                'tai_khoan' => function ($query) use ($khoaHocID) {
                    return $query->with([
                        'lop_hoc' => function ($query) use ($khoaHocID) {
                            return $query->where('khoa_hoc_id', $khoaHocID);
                        },
                    ]);
                },
            ])->get()->map(function ($c) {
                $c['tai_khoan']['lop_hoc']->map(function ($d) {
                    $d->ten = $d->taoTen();
                    return $d;
                });
                return $c;
            });

        return response()->json([
            'data' => $thuMoi,
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $item = ThuMoi::create($request->all());

        return response()->json($item->toArray());
    }

    public function update(ThuMoi $thu_moi, Request $request)
    {
        $thu_moi->fill($request->all());
        $thu_moi->save();

        return response()->json(['result' => true]);
    }

    public function show(ThuMoi $thuMoi)
    {
        return response()->json($thuMoi);
    }

    public function destroy(ThuMoi $thu_moi)
    {
        $thu_moi->destroy();

        return response()->json(['result' => true]);
    }
}
