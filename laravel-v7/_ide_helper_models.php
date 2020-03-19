<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\LopHoc
 *
 * @property int $id
 * @property int $khoa_hoc_id
 * @property string $nganh
 * @property string $cap
 * @property string|null $doi
 * @property array|null $tro_giang
 * @property string|null $vi_tri_hoc
 * @property string|null $ghi_chu
 * @property string|null $tai_khoan_cap_nhat
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\KhoaHoc $khoa_hoc
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TaiKhoan[] $thanh_vien
 * @property-read int|null $thanh_vien_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc locDuLieu()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereCap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereDoi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereGhiChu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereKhoaHocId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereNganh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereTaiKhoanCapNhat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereTroGiang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LopHoc whereViTriHoc($value)
 */
	class LopHoc extends \Eloquent {}
}

namespace App{
/**
 * App\TaiKhoan
 *
 * @property string $id
 * @property string $mat_khau
 * @property string $loai_tai_khoan
 * @property string $trang_thai
 * @property string $gioi_tinh
 * @property string|null $ten_thanh
 * @property string $ho_va_ten
 * @property string $ten
 * @property string $ngay_sinh
 * @property string|null $ngay_rua_toi
 * @property string|null $ngay_ruoc_le
 * @property string|null $ngay_them_suc
 * @property string|null $email
 * @property string|null $dien_thoai
 * @property string|null $dia_chi
 * @property string|null $ghi_chu
 * @property string|null $giao_ho
 * @property int|null $gia_pha_id
 * @property string|null $tai_khoan_cap_nhat
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LopHoc[] $lop_hoc
 * @property-read int|null $lop_hoc_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereDiaChi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereDienThoai($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereGhiChu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereGiaPhaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereGiaoHo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereGioiTinh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereHoVaTen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereLoaiTaiKhoan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereMatKhau($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereNgayRuaToi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereNgayRuocLe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereNgaySinh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereNgayThemSuc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereTaiKhoanCapNhat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereTen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereTenThanh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereTrangThai($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TaiKhoan whereUpdatedAt($value)
 */
	class TaiKhoan extends \Eloquent {}
}

namespace App{
/**
 * App\KhoaHoc
 *
 * @property int $id
 * @property string $ngay_bat_dau
 * @property string $ngay_ket_thuc
 * @property int $so_dot_kiem_tra
 * @property int $so_lan_kiem_tra
 * @property int $ngung_diem_danh
 * @property int $cap_nhat_dot_kiem_tra
 * @property array $xep_hang
 * @property array $xep_loai
 * @property array $di_hoc
 * @property array $di_le
 * @property string|null $tai_khoan_cap_nhat
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereCapNhatDotKiemTra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereDiHoc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereDiLe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereNgayBatDau($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereNgayKetThuc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereNgungDiemDanh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereSoDotKiemTra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereSoLanKiemTra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereTaiKhoanCapNhat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereXepHang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\KhoaHoc whereXepLoai($value)
 */
	class KhoaHoc extends \Eloquent {}
}

namespace App{
/**
 * App\BaseModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BaseModel query()
 */
	class BaseModel extends \Eloquent {}
}

