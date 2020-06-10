<?php
namespace TNTT\Models;

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
        $aResult = [];
        if (0 < $dotKT && $dotKT <= $khoaHoc->so_dot_kiem_tra) {
            $aHocLuc = $this->whereIn('tai_khoan_id', $arrHocVien)
                ->where('khoa_hoc_id', $khoaHoc->id)
                ->where('dot', $dotKT)
                ->where('phan_loai', null)
                ->get();
            foreach ($aHocLuc as $hocLuc) {
                $aResult[] = [
                    'tai_khoan_id' => $hocLuc->tai_khoan_id,
                    'lan' => $hocLuc->lan,
                    'diem' => $hocLuc->diem,
                ];
            }
        }

        return $aResult;
    }

    public function luuHocLuc(LopHoc $lopHoc, $thieuNhiArr, $dot, $lan)
    {
        $khoaHocID = $lopHoc->khoa_hoc_id;
        foreach ($thieuNhiArr as $arrTmp) {
            $this->firstOrNew(['tai_khoan_id' => $arrTmp['id'], 'khoa_hoc_id' => $khoaHocID, 'dot' => $dot, 'lan' => $lan, 'phan_loai' => null])
            ->fill([
                'diem'        => $arrTmp['diem'],
            ])
            ->save();
        }
        $lopHoc->tinhDiemHocLuc();
    }
}
