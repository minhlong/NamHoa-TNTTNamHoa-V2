<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use \Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContracts;
use Tymon\JWTAuth\Contracts\JWTSubject;

class TaiKhoan extends BaseModel implements AuthenticatableContracts, JWTSubject
{
    use Authenticatable;

    /**
     * @var array
     */
    public static $loaiTaiKhoan = ['THIEU_NHI', 'HUYNH_TRUONG', 'SOEUR', 'LINH_MUC',];
    /**
     * @var array
     */
    public static $loaiTrangThai = ['HOAT_DONG', 'TAM_NGUNG',];
    /**
     * Indicates if the IDs are auto-incrementing.
     *
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
    protected $hidden = ['mat_khau', 'remember_token'];
    protected $table = 'tai_khoan';

    /**
     * Get the password for the user.
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        $khoaHoc = KhoaHoc::hienTai();
        $lopHoc  = $this->lop_hoc()->where('khoa_hoc_id', $khoaHoc->id)->first();

        return [
            'tai_khoan'            => $this->attributesToArray(),
            // 'phan_quyen'           => Auth::user()->getPhanQuyen(),
            'lop_hoc_hien_tai_id'  => $lopHoc ? $lopHoc->id : null,
            'khoa_hoc_hien_tai_id' => $khoaHoc ? $khoaHoc->id : null,
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function lop_hoc()
    {
        return $this->belongsToMany(LopHoc::class, 'taikhoan_lophoc', 'tai_khoan_id', 'lop_hoc_id')
            ->orderBy('khoa_hoc_id', 'DESC')
            ->withPivot('chuyen_can', 'hoc_luc', 'xep_hang', 'ghi_chu', 'tai_khoan_cap_nhat');
    }
}