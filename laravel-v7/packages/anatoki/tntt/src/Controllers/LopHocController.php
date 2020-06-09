<?php

namespace TNTT\Controllers;

use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use TNTT\Models\KhoaHoc;
use TNTT\Models\LopHoc;
use TNTT\Services\Excel\Exports\LopHocInserted;
use TNTT\Services\Excel\Exports\TongKetSheet;
use TNTT\Services\Excel\Imports\LopHocImport;

class LopHocController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['bindings'])->only([
            'show',
            'update',
            'getTongKet',
        ]);
        $this->middleware(['can:Lớp Học'])->only([
            'update',
            'importStep1',
            'importStep2',
            'importStep3',
        ]);
    }

    /**
     * Lay danh sach lop hoc cua 1 Khoa Hoc
     * @param $khoaHocID
     * @param  LopHoc  $lopHoc
     * @return JsonResponse
     */
    public function index($khoaHocID, LopHoc $lopHoc)
    {
        $lopHoc = $lopHoc->where('khoa_hoc_id', $khoaHocID)
            ->locDuLieu()
            ->get()
            ->load('huynh_truong')
            ->map(function ($c) {
                $c['ten'] = $c->taoTen();
                return $c;
            });

        return response()->json([
            'data' => $lopHoc,
        ]);
    }

    /**
     * Lay thong tin chi tiet cua 1 lop hoc
     * @param  LopHoc  $lopHoc
     * @return JsonResponse
     */
    public function show(LopHoc $lopHoc)
    {
        $lopHoc->load(['huynh_truong', 'hoc_vien']);
        $lopHoc->ten = $lopHoc->taoTen();

        return response()->json($lopHoc);
    }

    /**
     * Update file
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function importStep1(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $importer = new LopHocImport();
        Excel::import($importer, $request->file('file'));

        return response()->json([
            'data' => $importer->getResult(),
        ]);
    }

    public function importStep2(Request $request)
    {
        $request->validate([
            'data' => 'required',
        ]);

        $result = new Collection();
        $rows   = $request->data;
        $khoaId = KhoaHoc::hienTai()->id;

        DB::beginTransaction();
        foreach ($rows as $row) {
            try {
                /** @var LopHoc $lopHoc */
                $lopHoc = LopHoc::create(array_merge(['khoa_hoc_id' => $khoaId,], $row));
            } catch (Exception $e) {
                continue;
            }

            $lopHoc->ten = $lopHoc->taoTen();
            $result->push($lopHoc);
        }
        DB::commit();

        return response()->json([
            'data' => $result,
        ]);
    }

    /**
     * Download imported user
     *
     * @param  Request  $request
     * @return LopHocInserted
     */
    public function importStep3(Request $request)
    {
        $request->validate([
            'data' => 'required',
        ]);

        return new LopHocInserted($request->data);
    }

    public function getTongKet(LopHoc $lopHoc)
    {
        return response()->json(
            (new TongKetSheet($lopHoc))->getData()
        );
    }
}
