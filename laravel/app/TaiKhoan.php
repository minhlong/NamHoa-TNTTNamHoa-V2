<?php
namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContracts;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use App\Services\Library;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class TaiKhoan extends BaseModel implements AuthenticatableContracts
{
    use Authenticatable;
    use EntrustUserTrait { restore as private restoreA; }
    use SoftDeletes { restore as private restoreB; }

    /**
     * Fix Trail
     */
    public function restore()
    {
        $this->restoreA();
        $this->restoreB();
    }

    /**
     * @var bool
     */
    public $incrementing = false;
    /**
     * @var array
     */
    protected $fillable = [
        'loai_tai_khoan',
        'trang_thai',
        'gioi_tinh',
        'ten_thanh',
        'ho_va_ten',
        'ngay_sinh',
        'ngay_rua_toi',
        'ngay_ruoc_le',
        'ngay_them_suc',
        'email',
        'dien_thoai',
        'dia_chi',
        'ghi_chu',
        'giao_ho',
    ];
    /**
     * @var array
     */
    public static $loaiTaiKhoan = [
        'THIEU_NHI',
        'HUYNH_TRUONG',
        'SOEUR',
        'LINH_MUC',
    ];
    /**
     * @var array
     */
    public static $loaiTrangThai = [
        'HOAT_DONG',
        'TAM_NGUNG',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('tai_khoan');
        $this->setHidden(['mat_khau', 'remember_token']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gia_pha()
    {
        return $this->belongsTo(GiaPha::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function than_nhan()
    {
        $query = $this->hasMany(ThanNhan::class, 'gia_pha_id', 'gia_pha_id');
        $query->orderBy('updated_at', 'DESC');

        return $query;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lop_hoc()
    {
        return $this->belongsToMany(LopHoc::class, 'taikhoan_lophoc', 'tai_khoan_id', 'lop_hoc_id')
            ->orderBy('khoa_hoc_id', 'DESC')
            ->withPivot('chuyen_can', 'hoc_luc', 'xep_hang', 'ghi_chu', 'tai_khoan_cap_nhat');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function anh_em()
    {
        $query = $this->hasMany(TaiKhoan::class, 'gia_pha_id', 'gia_pha_id');
        $query->where('id', '<>', $this->id)->orderBy('ngay_sinh', 'DESC');

        return $query;
    }

    public function roles()
    {
        return $this->belongsToMany(Config::get('entrust.role'), Config::get('entrust.role_user_table'));
    }

    public function nhom_tai_khoan()
    {
        return $this->roles();
    }

    public function getPhanQuyen($arrOptions = null)
    {
        $arrPerms = [];
        foreach ($this->nhom_tai_khoan()->get() as $nhom) {
            $arrPerms = array_merge($arrPerms, $nhom->perms()->pluck('ten')->toArray());
        }
        // Nếu không có quyền điểm danh thì
        //    + Chỉ được điểm danh các lớp mình đang dạy của Khóa hiện tại
        //    + Chỉ được vào điểm các lớp mình đang dạy của Khóa hiện tại
        $perName = 'diem-danh';
        if (!in_array($perName, $arrPerms) && !empty($arrOptions)) {
            $arrOptions = array_merge([
                'lop_hoc_id' => '',
                'ngay_hoc'   => '',
                'dotKT'      => '',
            ], $arrOptions);
            $lopHoc = \Auth::user()->lop_hoc()
                ->where('lop_hoc_id', $arrOptions['lop_hoc_id'])
                ->first();
            if ($lopHoc) {
                if ($arrOptions['ngay_hoc'] && $arrOptions['ngay_hoc'] == $this->kiemtraNgayDiemDanh($lopHoc->khoa_hoc->ngung_diem_danh)) {
                    // Chi duoc phep cap nhat sau mot so ngay quy dinh theo Khoa Hoc
                    $arrPerms[] = $perName;
                } elseif ($arrOptions['dotKT'] && $arrOptions['dotKT'] == $lopHoc->khoa_hoc->cap_nhat_dot_kiem_tra) {
                    $arrPerms[] = $perName;
                }
            }
        }

        return $arrPerms;
    }

    /**
     * Get the password for the user.
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    public function beforeSave()
    {
        parent::beforeSave(); // TODO: Change the autogenerated stub
        // Update Ten
        $ten = explode(' ', trim($this->ho_va_ten));
        $this->ten = end($ten);
    }

    /**
     * @param $arrNewAnhEm
     */
    public function luuAnhEm($arrNewAnhEm = [])
    {
        // Remove old Anh/Em, which not exist in new List
        $oldAnhEm = $this->anh_em()->pluck('id');
        $arrNewAnhEm = array_merge($arrNewAnhEm, [$this->id]);
        self::whereIn('id', $oldAnhEm)->whereNotIn('id', $arrNewAnhEm)->update([
            'gia_pha_id' => null,
        ]);
        // Have no Anh Em
        if (count($arrNewAnhEm) == 1) {
            return;
        }
        // Get All Gia Pha of Anh Em
        $arrGiaPha = self::whereIn('id', $arrNewAnhEm)->pluck('gia_pha_id');
        $gia_pha_id = $arrGiaPha[0];
        if (!$gia_pha_id) {
            $giaPha = new \App\GiaPha();
            $giaPha->save();
            $gia_pha_id = $giaPha->id;
        }
        // Update Than Nhan and Anh Em has same Gia Pha
        self::whereIn('gia_pha_id', $arrGiaPha)->orWhereIn('id', $arrNewAnhEm)->update([
            'gia_pha_id' => $gia_pha_id,
        ]);
        ThanNhan::whereIn('gia_pha_id', $arrGiaPha)->update([
            'gia_pha_id' => $gia_pha_id,
        ]);
        // Remove All Gia Pha
        GiaPha::where('id', '<>', $gia_pha_id)->whereIn('id', $arrGiaPha)->delete();
    }

    /**
     * @param $loaiTaiKhoan
     * @return string
     */
    private function convertLoaiTaiKhoanToKey($loaiTaiKhoan)
    {
        $arrTmp = explode('_', $loaiTaiKhoan);
        if (count($arrTmp) > 1) {
            return substr($arrTmp[0], 0, 1) . substr($arrTmp[1], 0, 1);
        } else {
            return substr($arrTmp[0], 0, 1) . substr($arrTmp[0], -1, 1);
        }
    }

    /**
     * @param null $loaiTaiKhoan
     * @return int|string
     */
    private function taoID($loaiTaiKhoan = null, $khoaHocID = null)
    {
        if (in_array($loaiTaiKhoan, self::$loaiTaiKhoan) && $loaiTaiKhoan != 'THIEU_NHI') {
            $sNewID = $this->convertLoaiTaiKhoanToKey($loaiTaiKhoan);
        } else {
            if (!$khoaHocID) {
                $khoaHocID = KhoaHoc::hienTaiHoacTaoMoi()->id;
            }
            $sNewID = substr($khoaHocID, -2);
        }
        $iCountID = self::where('id', 'like', "$sNewID%")->max('id');
        $iCountID = $iCountID ? (int)substr($iCountID, -3) : 0;
        ++$iCountID;
        $sNewID .= strlen($iCountID) < 3 ? str_repeat(0, 3 - strlen($iCountID)) . $iCountID : $iCountID;

        return $sNewID;
    }

    /**
     * @param $arrAttribute
     * @return TaiKhoan
     */
    public static function taoTaiKhoan($arrAttribute)
    {
        $arrAttribute = array_merge([
            'loai_tai_khoan' => null,
            'ho_va_ten'      => 'Họ và Tên',
        ], $arrAttribute);
        $taiKhoan = new self($arrAttribute);
        $taiKhoan->trang_thai = 'HOAT_DONG';
        $taiKhoan->id = $taiKhoan->taoID($taiKhoan->loai_tai_khoan);
        $taiKhoan->capNhatMatKhau($taiKhoan->id);
        if (!$taiKhoan->save()) {
            abort(500);
        }

        return $taiKhoan;
    }

    /**
     * @param $matKhau
     */
    public function capNhatMatKhau($matKhau)
    {
        if (env('APP_ENV') == 'dev') {
            $matKhau = 123456;
        }
        $this->mat_khau = bcrypt($matKhau);
    }

    /**
     * @param $query
     */
    public function scopeLocDuLieu($query)
    {
        if ($arrID = \Request::get('id')) {
            if (is_array($arrID)) {
                $query->whereIn('id', $arrID);
            } else {
                $query->where('id', 'like', '%' . $arrID . '%');
            }
        }
        if ($q = \Request::get('trang_thai')) { $query->where('trang_thai', $q); }
        if ($q = \Request::get('loai_tai_khoan')) { $query->whereIn('loai_tai_khoan', $q); }
        if ($q = \Request::get('ho_va_ten')) { $query->where('ho_va_ten', 'like', '%' . $q . '%'); }
        if ($q = \Request::get('ngay_sinh')) { $query->where('ngay_sinh', '=', $q); }
        if ($q = \Request::get('created_at')) { $query->where('created_at', '=', $q); }

        $khoa = \Request::get('khoa');
        $nganh = \Request::get('nganh');
        $cap = \Request::get('cap');
        $doi = \Request::get('doi');
        if ($khoa || $nganh || $cap || $doi) {
            $query->whereHas('lop_hoc', function ($query) use ($khoa, $nganh, $cap, $doi) {
                if ($khoa) {
                    $query->where('khoa_hoc_id', $khoa);
                }
                if ($nganh) {
                    $query->where('nganh', $nganh);
                }
                if ($cap) {
                    $query->where('cap', $cap);
                }
                if ($doi) {
                    $query->where('doi', $doi);
                }
            });
        }
    }

    /**
     * @param array $arrInfo
     * @return static
     */
    public function luuThanNhan(array $arrInfo)
    {
        if (!$this->gia_pha_id) {
            $giaPha = new \App\GiaPha();
            $giaPha->save();
            $this->gia_pha()->associate($giaPha);
            $this->save();
        }
        $arrInfo['gia_pha_id'] = $this->gia_pha->id;
        // Update or Create New
        if (isset($arrInfo['id'])) {
            $thanNhan = ThanNhan::findOrFail($arrInfo['id']);
            $thanNhan->fill($arrInfo);
            $thanNhan->save();
        } else {
            $thanNhan = ThanNhan::create($arrInfo);
        }

        return $thanNhan;
    }

    /**
     * Duoc phep cap nhat diem vao ngay Chua Nhat, Thu 2, Thu 3.
     * @param int $ngungDiemDanh
     * @return array
     */
    private function kiemtraNgayDiemDanh($ngungDiemDanh = 3)
    {
        $endDate = time();
        $ngayDacBiet = strtotime("-$ngungDiemDanh day", $endDate);
        $weekdayNumber = 0; // Ngay Chua Nhat = 0;
        do {
            $ngayDacBiet += (24 * 3600);
        } while (date('w', $ngayDacBiet) != $weekdayNumber);

        return $ngayDacBiet <= $endDate ? date('Y-m-d', $ngayDacBiet) : false;
    }
}
