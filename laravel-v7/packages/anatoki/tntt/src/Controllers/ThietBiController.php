<?php

namespace TNTT\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TNTT\Models\ThietBi;

class ThietBiController extends Controller
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
        $this->middleware(['can:Thiết Bị'])->only([
            'store',
            'update',
            'destroy',
        ]);
    }

    /**
     * @param  ThietBi  $thietBi
     * @return Response
     */
    public function index(ThietBi $thietBi)
    {
        $thietBi = $thietBi->with(['tai_khoan'])->get();

        return response()->json([
            'data' => $thietBi,
        ]);
    }

    public function update(ThietBi $thiet_bi, Request $request)
    {
        $data = $request->validate([
            'ten'       => 'required',
            'ngay_muon' => 'nullable|date_format:Y-m-d',
            'ngay_tra'  => 'nullable|date_format:Y-m-d',
        ]);

        $thiet_bi->fill($data);
        $thiet_bi->save();

        return response()->json(['result' => true]);
    }

    public function store(ThietBi $thietBi, Request $request)
    {
        $data = $request->validate([
            'info.tai_khoan_id' => 'required',
            'info.ngay_muon'    => 'required|date_format:Y-m-d',
            'info.ngay_tra'     => 'required|date_format:Y-m-d',
        ]);

        $arrThietBi = $thietBi->whereIn('id', $request->get('devices'))->get();

        foreach ($arrThietBi as $item) {
            $item->fill($request->get('info'));
            $item->save();
        }
        return response()->json(['result' => true]);
    }

    public function destroy(ThietBi $thiet_bi)
    {
        $thiet_bi->delete();

        return response()->json(['result' => true]);
    }
}
