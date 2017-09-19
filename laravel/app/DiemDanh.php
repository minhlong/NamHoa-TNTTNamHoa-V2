<?php
namespace App;

class DiemDanh extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('diem_danh');
    }

    /**
     * @var array
     */
    protected $fillable = [
        'tai_khoan_id',
        'phan_loai',
        'ngay',
        'di_le',
        'di_hoc',
        'ghi_chu',
    ];

    /**
     * @param array $arrHocVien
     * @param $sDate
     * @return array
     */
    public function getChuyenCanData(array $arrHocVien, $sDate)
    {
        $aResult = [];
        if ($sDate) {
            foreach ($arrHocVien as $tai_khoan_id) {
                $aResult[$sDate][$tai_khoan_id] = [
                    'di_le'   => null,
                    'di_hoc'  => null,
                    'ghi_chu' => null,
                ];
            }
            $aChuyenCan = $this->whereIn('tai_khoan_id', $arrHocVien)
                ->where('ngay', $sDate)
                ->where('phan_loai', null)
                ->get();
            if (!$aChuyenCan->isEmpty()) {
                foreach ($aChuyenCan as $chuyenCan) {
                    $aResult[$chuyenCan->ngay][$chuyenCan->tai_khoan_id] = [
                        'di_le'   => $chuyenCan->di_le,
                        'di_hoc'  => $chuyenCan->di_hoc,
                        'ghi_chu' => $chuyenCan->ghi_chu,
                    ];
                }
            }
        }

        return $aResult;
    }

    /**
     * @param LopHoc $lopHoc
     */
    public function luuChuyenCan(LopHoc $lopHoc)
    {
        $arrData = \Request::all();
        foreach ($arrData as $date => $arrTmp) {
            foreach ($arrTmp as $taiKhoanID => $arrChuyenCan) {
                // Update old data to Logs
                $this->where('tai_khoan_id', $taiKhoanID)
                    ->where('ngay', $date)
                    ->where('phan_loai', null)
                    ->update(['phan_loai' => 'LOGS']);
                // Insert new Data
                if ($arrChuyenCan['di_le'] || $arrChuyenCan['di_hoc'] || $arrChuyenCan['ghi_chu']) {
                    $this->create([
                        'tai_khoan_id' => $taiKhoanID,
                        'ngay'         => $date,
                        'di_le'        => $arrChuyenCan['di_le'],
                        'di_hoc'       => $arrChuyenCan['di_hoc'],
                        'ghi_chu'      => $arrChuyenCan['ghi_chu'],
                    ]);
                }
            }
            /* Update Cache for Reporting on Dashboard Page - 2 Weeks*/
            $this->setCacheReport($date, $lopHoc->id);
        }
        $lopHoc->tinhDiemChuyenCan();
    }

    protected function setCacheReport($date, $lopHocID)
    {
        \Cache::put("chuyen-can.{$date}.{$lopHocID}", true, 20160);
    }

    protected function checkCacheReport($date, $lopHocID)
    {
        return \Cache::has("chuyen-can.{$date}.{$lopHocID}");
    }
}
