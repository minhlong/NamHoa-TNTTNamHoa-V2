<?php

use Illuminate\Database\Seeder;

class PhanQuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->command->info('Create fake data for Nhom Tai Khoan! Start');

        DB::table('nhom_tai_khoan')->delete();
        DB::table('phan_quyen')->delete();
        DB::table('taikhoan_nhomtaikhoan')->delete();
        DB::table('phanquyen_nhomtaikhoan')->delete();

        // Add Nhom Tai Khoan
        $model = new \App\NhomTaiKhoan();
        $model->loai = 'NHOM';
        $model->ten = 'Ban Học Tập';
        $model->ten_hien_thi = 'Ban Học Tập';
        $model->ghi_chu = $model->ten_hien_thi;
        $model->save();

        $model = new \App\NhomTaiKhoan();
        $model->loai = 'NHOM';
        $model->ten = 'Ban Sinh Họat';
        $model->ten_hien_thi = 'Ban Sinh Họat';
        $model->ghi_chu = $model->ten_hien_thi;
        $model->save();

        // Add Phan Quyen
        $model = new \App\PhanQuyen();
        $model->ten = 'tai-khoan';
        $model->ten_hien_thi = 'Tài Khoản';
        $model->ghi_chu = 'Thêm, xóa, chỉnh sửa thông tin tài khoản';
        $model->save();

        $model = new \App\PhanQuyen();
        $model->ten = 'lop-hoc';
        $model->ten_hien_thi = 'Lớp Học';
        $model->ghi_chu = 'Thêm, xóa, chỉnh sửa thông tin lớp học';
        $model->save();

        $model = new \App\PhanQuyen();
        $model->ten = 'diem-danh';
        $model->ten_hien_thi = 'Điểm Danh';
        $model->ghi_chu = 'Chuyên cần, học lực. Mặc định Huynh Trưởng dạy chính sẽ có quyền này';
        $model->save();

        $model = new \App\PhanQuyen();
        $model->ten = 'danh-gia-cuoi-nam';
        $model->ten_hien_thi = 'Tổng Kết Cuối Năm';
        $model->ghi_chu = 'Chỉnh sửa thông tin xếp hạng';
        $model->save();

        $model = new \App\PhanQuyen();
        $model->ten = 'he-thong';
        $model->ten_hien_thi = 'Hệ Thống';
        $model->ghi_chu = 'Thông tin khóa học, hệ thống';
        $model->save();

        $model = new \App\PhanQuyen();
        $model->ten = 'phan-quyen';
        $model->ten_hien_thi = 'Phân Quyền';
        $model->ghi_chu = 'Phân quyền hệ thống, nhóm tài khoản';
        $model->save();

        $nhomTaiKhoan = \App\NhomTaiKhoan::whereTenHienThi('Ban Học Tập')->first();
        $nhomTaiKhoan->phan_quyen()->attach(\App\PhanQuyen::lists('id'));

        $this->command->info('Create fake data for Nhom Tai Khoan! Finished');
    }
}
