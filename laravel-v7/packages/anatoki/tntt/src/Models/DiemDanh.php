<?php
namespace TNTT\Models;

class DiemDanh extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('diem_danh');
    }

    protected $fillable = [
        'tai_khoan_id',
        'phan_loai',
        'ngay',
        'di_le',
        'di_hoc',
        'ghi_chu',
    ];

    public function getChuyenCanData(array $arrHocVien, $sDate)
    {
        $aResult = [];
        $aChuyenCan = $this->whereIn('tai_khoan_id', $arrHocVien)
            ->where('ngay', $sDate)
            ->where('phan_loai', null)
            ->get(['tai_khoan_id','di_le','di_hoc','ghi_chu']);
        foreach ($aChuyenCan as $chuyenCan) {
            $aResult[] = $chuyenCan;
        }

        return $aResult;
    }

    public function luuChuyenCan(LopHoc $lopHoc, $taiKhoanArr, $date)
    {
        foreach ($taiKhoanArr as $arrChuyenCan) {
            $this->firstOrNew(['tai_khoan_id' => $arrChuyenCan['id'], 'ngay' => $date, 'phan_loai' => null])
            ->fill([
                'di_le'        => $arrChuyenCan['di_le'],
                'di_hoc'       => $arrChuyenCan['di_hoc'],
                'ghi_chu'      => $arrChuyenCan['ghi_chu'],
            ])
            ->save();
        }
        
        /* Lưu lại cache để detect những lớp đã/chưa điểm danh - 2 Weeks*/
        $this->daDiemDanh($date, $lopHoc->id);
        
        $lopHoc->tinhDiemChuyenCan();
    }

    protected function daDiemDanh($date, $lopHocID)
    {
        \Cache::store('file')->put("chuyen-can.{$date}.{$lopHocID}", true, 20160);
    }

    protected function chuaDiemDanh($date, $lopHocID)
    {
        return \Cache::store('file')->has("chuyen-can.{$date}.{$lopHocID}");
    }
}
