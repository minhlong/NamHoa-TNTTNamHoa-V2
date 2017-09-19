<?php

use Illuminate\Database\Seeder;

class PhanQuyenAddNhanXetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Create fake data for Phan Quyen Nhan Xet ! Start');

        $model = new \App\PhanQuyen();
        $model->ten = 'nhan-xet';
        $model->ten_hien_thi = 'Nhận xét cuối năm';
        $model->ghi_chu = 'Chỉnh sửa nhận xét cuối năm. Mặc định Huynh Trưởng dạy chính sẽ có quyền này';
        $model->save();

        $this->command->info('Create fake data for Phan Quyen Nhan Xet! Finished');
    }
}
