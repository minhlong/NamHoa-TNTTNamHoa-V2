<?php
namespace App;

class DiemSo extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('diem_so');
    }

    /**
     * @var array
     */
    protected $fillable = [
        'tai_khoan_id',
        'khoa_hoc_id',
        'phan_loai',
        'dot',
        'lan',
        'diem',
        'ghi_chu',
    ];

    public function getHocLuc(array $arrHocVien, KhoaHoc $khoaHoc, $dotKT)
    {
        if (0 < $dotKT && $dotKT <= $khoaHoc->so_dot_kiem_tra) {
            foreach ($arrHocVien as $TaiKhoanID) {
                for ($j = 0; $j < $khoaHoc->so_dot_kiem_tra; ++$j) {
                    $aResult[$TaiKhoanID][$dotKT][$j + 1] = null;
                }
            }
            $aHocLuc = $this->whereIn('tai_khoan_id', $arrHocVien)
                ->where('khoa_hoc_id', $khoaHoc->id)
                ->where('dot', $dotKT)
                ->where('phan_loai', null)
                ->get();
            foreach ($aHocLuc as $hocLuc) {
                $aResult[$hocLuc->tai_khoan_id][$hocLuc->dot][$hocLuc->lan] = $hocLuc->diem;
            }
        }

        return $aResult;
    }

    public function luuHocLuc(LopHoc $lopHoc)
    {
        $arrData = \Request::except('ghi_chu');
        $khoaHocID = $lopHoc->khoa_hoc_id;
        foreach ($arrData as $taiKhoanID => $arrTmp) {
            foreach ($arrTmp as $dot => $arrTmpTmp) {
                foreach ($arrTmpTmp as $lan => $diem) {
                    // Update old data to Logs
                    $this->where('tai_khoan_id', $taiKhoanID)
                        ->where('khoa_hoc_id', $khoaHocID)
                        ->where('dot', $dot)
                        ->where('lan', $lan)
                        ->update(['phan_loai' => 'LOGS']);
                    if (is_numeric($diem)) {
                        // Insert new Data
                        $this->create([
                            'tai_khoan_id' => $taiKhoanID,
                            'khoa_hoc_id'  => $khoaHocID,
                            'dot'          => $dot,
                            'lan'          => $lan,
                            'diem'         => $diem,
                            'ghi_chu'      => \Request::get('ghi_chu'),
                        ]);
                    }
                }
            }
        }
        $lopHoc->tinhDiemHocLuc();
    }
}
