<?php

namespace TNTT\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TNTT\Models\KhoaHoc;
use TNTT\ThietBi;
use Validator;

class ThietBiController extends Controller
{
    /**
     * @param  ThietBi  $thietBi
     * @return Response
     */
    public function getDanhSach(ThietBi $thietBi)
    {
        $thietBi   = $thietBi->with(['tai_khoan'])->get();

        return response()->json([
            'data' => $thietBi,
        ]);
    }

    public function post(ThietBi $thietBi)
    {
        $tmpRule = [
            'ten'       => 'required',
            'ngay_muon' => 'nullable|date_format:Y-m-d',
            'ngay_tra'  => 'nullable|date_format:Y-m-d',
        ];

        $validator = Validator::make(\Request::all(), $tmpRule, [
            'date_format' => 'Trường :attribute không đúng định dạng.',
        ]);
        $validator->setAttributeNames([
            'ten'       => 'Tên Thiết Bị',
            'ngay_muon' => 'Ngày Mượn',
            'ngay_tra'  => 'Ngày Trả',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $thietBi->fill(\Request::all());
        $thietBi->save();

        return response()->json(true);
    }

    public function postDangKy(ThietBi $thietBi, Request $request)
    {
        $tmpRule = [
            'tai_khoan_id' => 'required',
            'ngay_muon'    => 'required|date_format:Y-m-d',
            'ngay_tra'     => 'required|date_format:Y-m-d',
        ];

        $validator = Validator::make(array_merge($request->get('info')), $tmpRule, [
            'date_format' => 'Trường :attribute không đúng định dạng.',
        ]);
        $validator->setAttributeNames([
            'tai_khoan_id' => 'Huynh Trưởng',
            'ngay_muon'    => 'Ngày Mượn',
            'ngay_tra'     => 'Ngày Trả',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $arrThietBi = $thietBi->whereIn('id', $request->get('devices'))->get();

        foreach ($arrThietBi as $item) {
            $item->fill($request->get('info'));
            $item->save();
        }
        return response()->json(true);
    }

    public function delete(ThietBi $thietBi)
    {
        $thietBi->delete();

        return response()->json(true);
    }
}
