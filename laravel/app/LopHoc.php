<?php
namespace App;

class LopHoc extends BaseModel
{
    /**
     * @var int
     */
    public $chuyen_can = 10;
    /**
     * @var int
     */
    public $hoc_luc = 10;
    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'khoa_hoc_id',
        'nganh',
        'cap',
        'doi',
        'vi_tri_hoc',
        'ghi_chu',
        'nhan_xet',
    ];
    /**
     * @var array
     */
    protected $casts = [
        'tro_giang' => 'array',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('lop_hoc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function khoa_hoc()
    {
        return $this->belongsTo(KhoaHoc::class, 'khoa_hoc_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function thanh_vien()
    {
        return $this->belongsToMany(TaiKhoan::class, 'taikhoan_lophoc', 'lop_hoc_id', 'tai_khoan_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function huynh_truong()
    {
        return $this->thanh_vien()
            ->where('loai_tai_khoan', '<>', 'THIEU_NHI');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hoc_vien($orderName = true)
    {
        $query = $this->thanh_vien()
            ->where('loai_tai_khoan', 'THIEU_NHI')
            ->withPivot('chuyen_can', 'hoc_luc', 'xep_hang', 'ghi_chu', 'nhan_xet', 'tai_khoan_cap_nhat');
        if ($orderName) {
            return $query->orderBy('ten');
        }

        return $query;
    }

    /**
     * @param $query
     * @return LopHoc
     */
    public function scopeLocDuLieu($query)
    {
        if ($q = \Request::get('khoa')) {
            $query->where('khoa_hoc_id', $q);
        }
        if ($q = \Request::get('nganh')) {
            $query->where('nganh', $q);
        }
        if ($q = \Request::get('cap')) {
            $query->where('cap', $q);
        }
        if ($q = \Request::get('doi')) {
            $query->where('doi', $q);
        }
        $query->orderBy('khoa_hoc_id', 'desc');
        $query->orderBy('nganh', 'asc');
        $query->orderBy('cap', 'asc');
        $query->orderBy('doi', 'asc');
    }

    /**
     * Tạo Tên Lớp Học.
     * @param bool $disableTenKhoa
     * @return string
     */
    public function taoTen($disableTenKhoa = false)
    {
        $this->tenNganh = 'Ngành' . ' ' . ThongSo::layTenNganh($this->nganh);
        $this->tenCap = 'Cấp' . ' ' . ThongSo::layTenCap($this->cap);
        $strTen = $this->tenNganh . ' - ' . $this->tenCap;
        if ($this->doi) {
            $strTen .= ' - ' . 'Đội' . ' ' . ThongSo::layTenDoi($this->doi);
        }
        if (!$disableTenKhoa) {
            $tenKhoa = 'Khóa' . ' ' . $this->khoa_hoc_id;
            $strTen = $tenKhoa . ' - ' . $strTen;
        }

        return $strTen;
    }

    /**
     * @return bool
     */
    public function luuHuynhTruongTroGiang()
    {
        $arrHuynhTruong = $arrTroGiang = [];
        if ($arrTmpHuynhTruong = \Request::get('phu_trach')) {
            foreach ($arrTmpHuynhTruong as $data) {
                if (isset($data['id'])) {
                    $arrHuynhTruong[] = $data['id'];
                } else {
                    $arrTroGiang[] = $data['text'];
                }
            }
        }
        // Add Huynh Truong
        $this->huynh_truong()->sync($arrHuynhTruong, false); // Insert new Huynh Truong
        $arrDelete = $this->huynh_truong()->whereNotIn('tai_khoan_id', $arrHuynhTruong)
            ->pluck('tai_khoan_id')->toArray(); // Delete Huynh Truong
        if (!empty($arrDelete)) {
            $this->huynh_truong()->detach($arrDelete);
        }
        // Add Tro Giang
        $this->tro_giang = $arrTroGiang;

        return $this->save();
    }

    /**
     * Tinh Diem Chuyen Can.
     */
    public function tinhDiemChuyenCan()
    {
        $objKhoaHoc = $this->khoa_hoc;
        $arrHocVien = $this->hoc_vien()->get();
        $arrDiemDanh = DiemDanh::whereIn('tai_khoan_id', $arrHocVien->pluck('id'))
            ->where('phan_loai', null)
            ->whereBetween('ngay', [$objKhoaHoc->ngay_bat_dau, $objKhoaHoc->ngay_ket_thuc])
            ->get();
        $arrDiemTru = [];
        foreach ($arrDiemDanh as $item) {
            if (in_array($item->di_le, array_keys($objKhoaHoc->di_le))) {
                @$arrDiemTru[$item->tai_khoan_id] += $objKhoaHoc->di_le[$item->di_le];
            }
            if (in_array($item->di_hoc, array_keys($objKhoaHoc->di_hoc))) {
                @$arrDiemTru[$item->tai_khoan_id] += $objKhoaHoc->di_hoc[$item->di_hoc];
            }
        }
        foreach ($arrHocVien as &$taiKhoan) {
            $taiKhoan->pivot->chuyen_can = $this->chuyen_can;
        }
        foreach ($arrHocVien as $taiKhoan) {
            if (isset($arrDiemTru[$taiKhoan->id])) {
                $taiKhoan->pivot->chuyen_can = $this->chuyen_can + $arrDiemTru[$taiKhoan->id];
            }
        }
        foreach ($arrHocVien as $taiKhoan) {
            $taiKhoan->pivot->save();
        }
        $this->tinhTongKet();
    }

    public function tinhDiemHocLuc()
    {
        $objKhoaHoc = $this->khoa_hoc;
        $arrHocVien = $this->hoc_vien()->get();
        $arrHocLuc = DiemSo::select('tai_khoan_id', 'dot', \DB::raw('avg(diem) AS diemTB'))
            ->whereIn('tai_khoan_id', $arrHocVien->pluck('id'))
            ->where('phan_loai', null)
            ->whereNotNull('diem')
            ->where('khoa_hoc_id', $objKhoaHoc->id)
            ->groupBy(['tai_khoan_id', 'dot'])
            ->get();
        $arrStamp = [];
        foreach ($arrHocLuc as $item) {
            $arrStamp[$item->tai_khoan_id][$item->dot] = $item->diemTB;
        }
        foreach ($arrHocVien as $taiKhoan) {
            foreach ($arrStamp as $tai_khoan_id => $arrDiem) {
                if ($taiKhoan->id == $tai_khoan_id) {
                    $taiKhoan->pivot->hoc_luc = array_sum($arrDiem) / count($arrDiem);
                    $taiKhoan->pivot->save();
                }
            }
        }
        $this->tinhTongKet();
    }

    public function tinhTongKet()
    {
        $arrHocVien = $this->hoc_vien(false)
            ->select('*', \DB::raw('(hoc_luc + chuyen_can) as tong_diem'))
            ->where(function ($query) {
                $query->whereNull('xep_hang')
                    ->orWhere('xep_hang', '<>', 'KHONG_XEP_HANG');
            })
            ->orderBy('tong_diem', 'desc')
            ->orderBy('hoc_luc', 'desc')
            ->get();
        $arrHang = [
            'I'            => 0,
            'II'           => 0,
            'III'          => 0,
            'KHUYEN_KHICH' => 0,
            'LEN_LOP'      => 0,
        ];
        foreach ($arrHocVien as $taiKhoan) {
            $tmpHang = 'O_LAI_LOP';
            foreach ($arrHang as $Hang => $item) {
                if ($taiKhoan->pivot->chuyen_can >= $this->khoa_hoc->xep_hang['CHUYEN_CAN'][$Hang] &&
                    $taiKhoan->pivot->hoc_luc >= $this->khoa_hoc->xep_hang['HOC_LUC'][$Hang]
                ) {
                    if (isset($this->khoa_hoc->xep_hang['SO_LUONG'][$Hang]) && $arrHang[$Hang] >= $this->khoa_hoc->xep_hang['SO_LUONG'][$Hang]) {
                        continue;
                    }
                    $tmpHang = $Hang;
                    break;
                }
            }
            if (isset($arrHang[$tmpHang])) {
                ++$arrHang[$tmpHang];
            }
            $taiKhoan->pivot->xep_hang = $tmpHang;
            $taiKhoan->pivot->save();
        }

        return $arrHang;
    }
}
